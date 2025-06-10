<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Letter Head Layout</title>
    <style>
        .d-flex {
            display: flex !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .justify-content-between {
            justify-content: space-between !important;
        }

        .border-bottom {
            border-bottom: 1px solid #000 !important;
        }

        .border-top {
            border-top: 1px solid #000 !important;
        }

        .border-dark {
            border-color: #212529 !important;
        }

        .p-3 {
            padding: 1rem !important;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        header {
            margin-bottom: 30px;
        }

        footer {
            margin-top: 30px;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        .content-section {
            padding: 20px 0;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <header>
        <nav class="border-bottom border-dark p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <img src="{{ asset('assets/images/ve_logo.png') }}" alt="VolksEnergies Logo" width="100px">
                </div>
                <div>
                    <p>
                        B1/D8, 2nd floor,<br>
                        Mohan Cooperative Industrial Estate,<br>
                        New Delhi - 110044<br>
                    </p>
                    <p>
                        ☎️ +91 9650393636, +91 9654551781,<br>
                        ☎️ Accounts: +91 8882591733<br>
                        📧 contact@volksenergies.in<br>
                        🌐 www.volksenergies.in
                    </p>
                </div>
            </div>
        </nav>
    </header>
    @yield('pdf-content')
    <footer class="border-top border-dark p-3">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <p>CIN: U40100DL2011PTC228907</p>
            </div>
            <div>
                <p>PAN: AADCV9396C</p>
            </div>
            <div>
                <p>MSME: UDYAM-DL-090000465</p>
            </div>
        </div>
    </footer>
</body>

</html>
