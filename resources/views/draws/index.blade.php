<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lotto Drawings</title>
</head>
<body>
<h1>Updated Lotto Drawings</h1>
<ul>
    @foreach ($draws as $draw)
        <li>{{ $draw->year }} - {{ $draw->draw_date }} - {{ $draw->draw_type }}</li>
    @endforeach
</ul>
</body>
</html>
