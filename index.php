<?php
function tokenizeText($text)
{
    $words = preg_split('/[\s,.-]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    return $words;
}

function calculateWordFrequency($text, $stopWords)
{

    $text = strtolower($text);
    $words = tokenizeText($text);
    $filteredWords = array_diff($words, $stopWords);
    $wordFrequency = array_count_values($filteredWords);
    arsort($wordFrequency);
    return $wordFrequency;
}
$stopWords = array("the", "and", "in", "on" ,"to", "of", "a", "is", "it", "that", "an", "are", "just", "what","then");
$textError = $limitError = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text = $_POST["text"];
    $sortOrder = $_POST["sort"];
    $limit = $_POST["limit"];


    if (empty($text)) {
        $textError = 'Please enter some text.';
    }

    if (!in_array($sortOrder, array('asc', 'desc'))) {
        $sortOrder = 'desc'; 
    }


    if (!is_numeric($limit) || $limit < 1) {
        $limitError = 'Please enter a valid limit (greater than 0).';
    }

    if (empty($textError) && empty($limitError)) {

        $wordFrequency = calculateWordFrequency($text, $stopWords);


        if ($sortOrder === 'asc') {
            asort($wordFrequency); 
        }

        $wordFrequency = array_slice($wordFrequency, 0, $limit);

        echo "<h2>Word Frequency Counter</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Word</th><th>Frequency</th></tr>";
        foreach ($wordFrequency as $word => $frequency) {
            echo "<tr><td>$word</td><td>$frequency</td></tr>";
        }
        echo "</table>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Word Frequency Counter</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
<h1>Word Frequency Counter</h1>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <label for="text">Paste or type your text here:</label><br>
    <textarea id="text" name="text" rows="10" cols="50" required><?php if (isset($_POST['text'])) echo $_POST['text']; ?></textarea><br>
    <span class="error"><?php echo $textError; ?></span><br>

    <label for="sort">Sort by frequency:</label>
    <select id="sort" name="sort">
        <option value="asc" <?php if (isset($_POST['sort']) && $_POST['sort'] == 'asc') echo 'selected'; ?>>Ascending</option>
        <option value="desc" <?php if (!isset($_POST['sort']) || (isset($_POST['sort']) && $_POST['sort'] == 'desc')) echo 'selected'; ?>>Descending</option>
    </select><br>

    <label for="limit">Number of words to display:</label>
    <input type="number" id="limit" name="limit" value="<?php echo isset($_POST['limit']) ? $_POST['limit'] : '10'; ?>" min="1"><br>
    <span class="error"><?php echo $limitError; ?></span><br>

    <input type="submit" value="Calculate Word Frequency">
</form>

</body>
</html>
