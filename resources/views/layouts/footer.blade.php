<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <style>
        .footer {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #f4f4f4;
            border-top: 1px solid #ddd;
            font-family: Arial, sans-serif;
        }
        .footer div {
            margin: 0 10px;
        }
        .logout-dropdown {
            position: relative;
            display: inline-block;
        }
        .logout-dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .logout-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .logout-dropdown-content a:hover {background-color: #f1f1f1}
        .logout-dropdown:hover .logout-dropdown-content {display: block;}
    </style>
</head>
<body>
    <div class="footer">
        <div>
            <img src="{{ asset('favicon.ico') }}" alt="Favicon" style="width: 30px; height: 30px;"> 
            {{ 'TUVAT BD Certificate Verification System v2.0.0' }}
        </div>
        <div>
            @auth
            {{ 'Developed in-house by ' }}<a href="mailto:swad.mahfuz@gmail.com">Swad Ahmed Mahfuz</a> &copy; 2024
            @endauth
        </div>
        <div>
            {{-- @auth
                <div class="logout-dropdown">
                    <span>{{ Auth::user()->name }}</span>
                    <div class="logout-dropdown-content">
                        <a href="{{ route('logout') }}">Logout</a>
                    </div>
                </div>
            @endauth --}}
        </div>
    </div>
</body>
</html>
