<?php
echo "<h1>PHP Basic Examples</h1>";
echo "<hr>";
?>

<!-- ===================== 1. HELLO WORLD ===================== -->
<h2>1. Hello World</h2>
<?php
echo "Hello World!";
?>

<hr>

<!-- ===================== 2. VARIABLES ===================== -->
<h2>2. Variables</h2>
<?php
$name = "Ahmed";
$age = 20;

echo "Name: " . $name . "<br>";
echo "Age: " . $age;
?>

<hr>

<!-- ===================== 3. SUM ===================== -->
<h2>3. Sum of Two Numbers</h2>
<?php
$a = 10;
$b = 5;
$sum = $a + $b;

echo "Sum = " . $sum;
?>

<hr>

<!-- ===================== 4. IF CONDITION ===================== -->
<h2>4. If Condition</h2>
<?php
$age = 18;

if ($age >= 18) {
    echo "You are an adult";
} else {
    echo "You are a minor";
}
?>

<hr>

<!-- ===================== 5. FOR LOOP ===================== -->
<h2>5. For Loop</h2>
<?php
for ($i = 1; $i <= 5; $i++) {
    echo "Number: $i <br>";
}
?>

<hr>

<!-- ===================== 6. WHILE LOOP ===================== -->
<h2>6. While Loop</h2>
<?php
$i = 1;

while ($i <= 5) {
    echo "Count: $i <br>";
    $i++;
}
?>

<hr>

<!-- ===================== 7. ARRAY ===================== -->
<h2>7. Array</h2>
<?php
$fruits = array("Apple", "Banana", "Orange");

echo $fruits[0];
?>

<hr>

<!-- ===================== 8. FOREACH ===================== -->
<h2>8. Foreach Loop</h2>
<?php
$fruits = array("Apple", "Banana", "Orange");

foreach ($fruits as $fruit) {
    echo $fruit . "<br>";
}
?>

<hr>

<!-- ===================== 9. FUNCTION ===================== -->
<h2>9. Function</h2>
<?php
function greet($name) {
    return "Hello " . $name;
}

echo greet("Ahmed");
?>

<hr>

<!-- ===================== 10. FORM HANDLING ===================== -->
<h2>10. Simple Form</h2>

<form method="post">
    <input type="text" name="username" placeholder="Enter name">
    <input type="submit" value="Send">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST["username"];
    echo "Welcome " . $user;
}
?>