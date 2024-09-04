<?php
require_once('../initialize.php');

// DBConnection class
class DBConnection
{
    private $host = DB_SERVER;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    private $database = DB_NAME;
    public $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die('Connection failed: ' . $this->conn->connect_error);
        }
    }

    public function __destruct()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

$db = new DBConnection();
$conn = $db->conn;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container { margin-top: 20px; }
        .alert { display: none; }
        .wait-message { display: none;text-align: center;margin: 20px;color:blue }
    </style>
</head>
<body>
    <div class="container" style="max-width: 500px;">
        <h2 class="text-center mb-4">Subscription Handle</h2>

        <div class="alert alert-success" id="success-alert">Subscription updated successfully</div>
        <div class="alert alert-danger" id="error-alert">Failed to update subscription</div>
        <div class="wait-message" id="wait-message">Reloading, please wait...</div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $subscription_type = $conn->real_escape_string($_POST['subscription_type'] ?? '');
            $start_date = $conn->real_escape_string($_POST['start_date'] ?? '');
            $expiry_date = $conn->real_escape_string($_POST['expiry_date'] ?? '');
            $status = $conn->real_escape_string($_POST['status'] ?? '');
            $id = (int) ($_POST['id'] ?? 0);
            $secret_code = $conn->real_escape_string($_POST['secret_code'] ?? '');

            $result = $conn->query("SELECT secret_code FROM subscriptions WHERE id = $id");

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();

                if ($secret_code === $row['secret_code']) {
                    $sql = "UPDATE subscriptions 
                            SET subscription_type = '$subscription_type', 
                                start_date = '$start_date', 
                                expiry_date = '$expiry_date', 
                                status = '$status' 
                            WHERE id = $id";

                    if ($conn->query($sql) === TRUE) {
                        echo '<script>
                                document.getElementById("wait-message").style.display = "block";
                                    document.getElementById("success-alert").style.display = "block";
                                setTimeout(() => {
                                    setTimeout(() => window.location.replace(window.location.href), 2000);
                                }, 1000);
                              </script>';
                    } else {
                        echo '<script>
                                document.getElementById("wait-message").style.display = "block";
                                    document.getElementById("error-alert").style.display = "block";
                                setTimeout(() => {
                                    setTimeout(() => window.location.replace(window.location.href), 2000);
                                }, 1000);
                              </script>';
                    }
                } else {
                    echo '<script>
                            document.getElementById("wait-message").style.display = "block";
                                document.getElementById("error-alert").innerText = "Invalid secret code! Update not allowed.";
                                document.getElementById("error-alert").style.display = "block";
                            setTimeout(() => {
                                setTimeout(() => window.location.replace(window.location.href), 2000);
                            }, 1000);
                          </script>';
                }
            } else {
                echo '<script>
                        document.getElementById("wait-message").style.display = "block";
                            document.getElementById("error-alert").innerText = "No subscription found with the given ID.";
                            document.getElementById("error-alert").style.display = "block";
                        setTimeout(() => {
                            setTimeout(() => window.location.replace(window.location.href), 2000);
                        }, 1000);
                      </script>';
            }
            exit();
        }

        $result = $conn->query("SELECT * FROM subscriptions WHERE id = 1");
        $row = $result->fetch_assoc();
        ?>

        <form id="subscription-form" method="post">
            <input type="hidden" name="id" value="1">
            <div class="form-group">
                <label for="subscription_type">Subscription Type:</label>
                <input type="text" class="form-control" name="subscription_type" id="subscription_type" value="<?php echo htmlspecialchars($row['subscription_type'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="form-group">
                <label for="start_date">Start Date:</label>
                <input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo htmlspecialchars($row['start_date'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="form-group">
                <label for="expiry_date">Expiry Date:</label>
                <input type="date" class="form-control" name="expiry_date" id="expiry_date" value="<?php echo htmlspecialchars($row['expiry_date'] ?? '', ENT_QUOTES); ?>" required>
            </div>
            <div class="form-group">
                <label for="status">Status:</label>
                <select class="form-control" name="status" id="status" required>
                    <option value="active" <?php echo (isset($row['status']) && $row['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="expired" <?php echo (isset($row['status']) && $row['status'] == 'expired') ? 'selected' : ''; ?>>Expired</option>
                    <option value="cancelled" <?php echo (isset($row['status']) && $row['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div class="form-group">
                <label for="secret_code">Secret Code:</label>
                <input type="password" placeholder="******" class="form-control" name="secret_code" id="secret_code" required>
            </div>
            <button type="submit" class="btn btn-danger w-100">Update</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
