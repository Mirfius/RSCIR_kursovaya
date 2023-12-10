<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>

<div class="container">
    <h2>Edit Book</h2>
    <form id="editBookForm" method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $bookToEdit['title']; ?>" required>
        <br>
        <label for="link">Link:</label>
        <input type="text" id="link" name="link" value="<?php echo $bookToEdit['link']; ?>" required>
        <br>
        <label for="description">Description:</label>
        <input type="text" id="description" name="description" value="<?php echo $bookToEdit['description']; ?>">
        <br>
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="edited_book_id" value="<?php echo $bookToEdit['book_id']; ?>">
        <input type="submit" value="Update Book">
    </form>
</div>

</body>
</html>
