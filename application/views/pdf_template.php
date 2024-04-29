<!DOCTYPE html>
<html>
<head>
    <title>PDF Export</title>
</head>
<body>
    <h1>PDF Content</h1>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
        <?php foreach ($records as $record): ?>
            <tr>
                <td><?php echo $record->mpc_id; ?></td>
                <td><?php echo $record->mpc_name; ?></td>
                <td><?php echo $record->mpc_name; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>