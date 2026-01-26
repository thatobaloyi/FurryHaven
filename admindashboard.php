<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<html>

<head>
    <title>Admin Dashboard</title>
</head>

<body>
    <h1>Welcome, <?php echo "$_SESSION[first_name] $_SESSION[last_name]" ?></h1>

    <h2>Registered Animals</h2>
    <button onclick="location.href='/animals/register'">+ New</button>

    <form method="GET" action="/">
        <input type="text" name="search" placeholder="Search by name..." value="<?php echo $search; ?>">
        <select name="filter">
            <option value="">-- Filter By --</option>
            <option value="Adult">Adult</option>
            <option value="Juvenile">Juvenile</option>
            <option value="Senior">Senior</option>
            <option value="Dog">Dog</option>
            <option value="Cat">Cat</option>
        </select>
        <button type="submit">Search / Filter</button>
    </form>

    <table border="1" cellpadding="5">
        <tr>
            <th></th> <!-- Image -->
            <th>Name</th>
            <th>Adoption Status</th>
            <th>Applications</th>
            <th>Species</th>
            <th>Gender</th>
            <th>Intake Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr onclick="showDetails('<?php echo $row['Animal_ID']; ?>')">
                <td><img src="images/<?php echo $row['Animal_ID']; ?>.jpg" width="50"></td>
                <td><?php echo $row['Animal_Name']; ?></td>
                <td><?php echo $row['outtakeType'] ? 'Adopted' : 'Available'; ?></td>
                <td><?php echo rand(0, 5); ?></td>
                <td><?php echo $row['Animal_Type']; ?></td>
                <td><?php echo $row['Animal_Gender']; ?></td>
                <td><?php echo date("d-M-Y", strtotime($row['Animal_RescueDate'])); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Popup -->
    <div id="animalPopup" style="display:none; position:fixed; top:10%; left:20%; background:white; border:1px solid black; padding:20px; z-index:1000;">
        <div id="popupContent"></div>
        <button onclick="closePopup()">Close</button>
    </div>

    <script>
        function showDetails(animalID) {
            fetch('?details=' + animalID)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('popupContent').innerHTML = data;
                    document.getElementById('animalPopup').style.display = 'block';
                });
        }

        function closePopup() {
            document.getElementById('animalPopup').style.display = 'none';
        }
    </script>




</body>

</html>