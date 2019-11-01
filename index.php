<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>8 Sheet Helper</title>
</head>
<body>
    <form action="result.php" method="POST">
        <p><select name="type">
                <option value="zte">ZTE</option>
                <option value="zyxel">Zyxel</option>
                <option value="huawei">Huawei</option>
            </select>
            Location ID <input type="text" name="locationID" value="">
            <!--OLT <input type="text" name="olt" value=""> -->
        </p>
            <textarea name="detail" rows="20" cols="100"></textarea>
            <p><button type="submit" id="button">Generate</button></p>
    </form>
</body>
</html>
