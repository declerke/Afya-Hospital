<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AFYA Hospital</title>
    <link rel="stylesheet" href="signupstyling.css">
    <script>
        function toggleStaffID() {
            var role = document.getElementById("role").value;
            var staffIDField = document.getElementById("staffIDField");
            if (role === "doctor" || role === "nurse" || role === "hospital_staff") {
                staffIDField.style.display = "block";
            } else {
                staffIDField.style.display = "none";
            }
        }

        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            if (password.length < 8) {
                alert("Password must be at least 8 characters long!");
                return false;
            }
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="register-container">
        <h2>AFYA HOSPITAL</h2>
        <form action="signupprocess.php" method="POST" onsubmit="return validatePassword();">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth:</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="number" id="phonenumber" name="phone" placeholder="Phone Number" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="postaladress" name="address" placeholder="Postal Address" required>
                </div>
                <div class="form-group">
                    <select id="role" name="role" onchange="toggleStaffID()" required>
                        <option value="">Select Role</option>
                        <option value="patient">Patient</option>
                        <option value="doctor">Doctor</option>
                        <option value="nurse">Nurse</option>
                        <option value="hospital_staff">Hospital Staff</option>
                    </select>
                    <div id="staffIDField" style="display:none;">
                        <input type="text" name="staff_id" placeholder="Staff ID">
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                </div>
            </div>
            <button type="submit">Sign Up</button>
        </form>
    </div>
</body>
</html>
