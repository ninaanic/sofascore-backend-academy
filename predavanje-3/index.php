<?php
// 1. primjer
//var_dump($_SERVER);


// 2. primjer
//var_dump($_GET);


// 3. primjer -- ne radi
//http_response_code(400);


// 4. primjer -- ne radi skroz točno
/*
header('Content-Type: text/plain');
header('My-Header: header value');
setcookie('My-Cookie', 'cookie value');

echo 'Hello', PHP_EOL, 'World';
*/


// 5. primjer

session_start();

$_SESSION['logged_in'] ??= false;
$logInError = false;

if (
    'POST' === ($_SERVER['REQUEST_METHOD'] ?? '')
    && isset($_POST['username'])
    && isset($_POST['password'])
) {
    if (
        'user' === $_POST['username']
        && 'pass' === $_POST['password']
    ) {
        $_SESSION['logged_in'] = true;
    } else {
        $logInError = true;
    }
}

?>

<?php if ($_SESSION['logged_in']): ?>
    <div>You are logged in!</div>
<?php else: ?>
    <?php if ($logInError): ?>
        <div>Invalid login data!</div>
    <?php endif ?>
    <form method="POST">
        <input type="text" name="username"
               value="<?= $_POST['username'] ?? '' ?>"
               placeholder="username"
        >
        <input type="password" name="password"
               placeholder="password"
        >
        <button type="submit">Submit</button>
    </form>
<?php endif ?>
