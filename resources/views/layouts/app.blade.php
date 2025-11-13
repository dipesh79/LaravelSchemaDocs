<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Schema Docs')</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            background: #fafafa;
            color: #222;
            font-family: Arial, sans-serif;
        }

        nav {
            background: #0b5fff;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        nav .links {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        nav a {
            color: #fff;
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            transition: background 0.2s ease;
        }

        nav a.active,
        nav a:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        main.container {
            flex: 1; /* pushes footer down */
            padding: 20px;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        footer {
            background: #f4f6f8;
            color: #555;
            padding: 12px 20px;
            text-align: center;
            font-size: 0.9rem;
            border-top: 1px solid #e5e7eb;
        }

        footer a {
            color: #0b5fff;
            text-decoration: none;
            font-weight: 500;
        }

        footer a:hover {
            text-decoration: underline;
        }

        .page-title {
            margin-top: 6px;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
@include('laravelschemadocs::partials.navbar')

<main class="container">
    @yield('content')
</main>

@include('laravelschemadocs::partials.footer')
</body>
</html>
