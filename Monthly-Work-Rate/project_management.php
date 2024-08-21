<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ana Sayfa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .baslik {
            color: black;
            text-align: center;
            width: 800px;
            height: 50px;
            margin-bottom: 20px;
            font-size: 40px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 600px;
            margin: 10px;
            text-align: center;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .link-container {
            display: flex;
            justify-content: center;
            gap: 20px; /* Adds space between links */
            margin-bottom: 10px; /* Adds bottom margin */
        }
        .link-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .link {
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            text-transform: uppercase;
            width: 360px; /* Sets width */
            margin: 5px 0; /* Adds top and bottom margin */
        }
        .link2 {
            padding: 10px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            text-transform: uppercase;
            width: 100px; /* Sets width */
            height: 20px;
            margin-top: 5px;
        }
        .link2:hover {
            background-color: red;
        }
        .link:hover {
            background-color: #0056b3;
        }

        .additional-links {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px; /* Adds top margin */
        }
        .additional-link {
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            text-transform: uppercase;
            width: 500px; /* Sets width */
            margin: 5px 0; /* Adds top and bottom margin */
        }
        .additional-link:hover {
            background-color: #0056b3;
        }
        .logout {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #dc3545;
            padding: 10px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-transform: uppercase;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }
        .logout:hover {
            background-color: #c82333;
        }
        .add-admin-link {
            position: absolute; /* Position it at the top left */
            top: 20px;
            left: 20px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-transform: uppercase;
        }
        .add-admin-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout">Çıkış Yap</a>
    <a href="add_admin.php" class="add-admin-link"> Admin Ekle</a> <!-- Add Admin Link -->
    <div class="wrapper">
        <div class="baslik">PROJE İŞLEMLERİ</div>
        <div class="container">
            <div class="additional-links">
                <a href="search_project.php" class="additional-link">Proje&nbsp; Veya Kişi Ara</a>
            </div>
            <div class="link-container">
                <a href="add_project.php" class="link">Proje Ekle</a>
                <a href="delete_project.php" class="link2"> Sil</a>
            </div>
            <div class="link-container">
                <a href="add_person.php" class="link">Kişi Ekle</a>
                <a href="delete_person.php" class="link2">Kişi Sil</a>
            </div>
            <div class="additional-links">
                <a href="add_projectPerson.php" class="additional-link">Projeye Kişi Ekle</a>
                <a href="add_monthly_work_rate.php" class="additional-link">Aylık Çalışma Oranı Ekle</a>
                <a href="view_monthly_work_rates.php" class="additional-link">Aylık Çalışma Oranlarını Görüntüle</a>
                <a href="change_password.php" class="additional-link">Şifre Değiştir</a>
            </div>
        </div>
    </div>
</body>
</html>
