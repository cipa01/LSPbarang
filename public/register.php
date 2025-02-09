<?php
require_once '../app/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Check if the role is 'admin' before proceeding
    if ($role !== 'admin') {
        $error = "Hanya admin yang bisa mendaftar.";
    } else {
        $stmt = $db->prepare("INSERT INTO user (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Pendaftaran gagal.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Register</title>
</head>

<body class="flex items-center justify-center h-screen bg-gray-200">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-2xl mb-6">Register</h2>
        <?php if (isset($error)): ?>
            <div class="text-red-500 mb-4"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" class="border rounded w-full py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="border rounded w-full py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="border rounded w-full py-2 px-3" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Role</label>
                <select name="role" class="border rounded w-full py-2 px-3" required>
                    <option value="admin">Admin</option>
                    <option value="petugas">Petugas</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-500 text-white rounded py-2 px-4">Register</button>
            <a href="login.php" class="text-blue-500 hover:underline mt-4 block">Already have an account? Login here.</a>
        </form>
    </div>
</body>

</html>