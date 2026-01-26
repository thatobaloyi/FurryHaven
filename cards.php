<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cards Section</title>
  <style>
    body {
      font-family: Inter, ui-sans-serif, Arial, sans-serif;
      background-color: #fdf4e6; /* beige background */
      margin: 0;
      padding: 0;
    }

    .cards {
      display: flex;
      justify-content: center;
      align-items: stretch;
      gap: 20px;
      padding: 40px;
      flex-wrap: wrap;
    }

    .card {
      flex: 1;
      min-width: 220px;
      padding: 30px;
      border-radius: 12px;
      text-align: center;
      color: #fff;
      box-shadow: 0 6px 12px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card h2 {
      margin: 15px 0 10px;
      font-size: 22px;
      font-weight: bold;
      text-transform: uppercase;
    }

    .card p {
      font-size: 15px;
      line-height: 1.5;
    }

    /* Exact colors from design */
    .adopt {
      background-color: #1c3b5a; /* navy */
    }
    .donate {
      background-color: #da7422; /* orange */
    }
    .volunteer {
      background-color: #7a7f52; /* olive green */
    }

    /* Icon placeholder */
    .icon {
      font-size: 40px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>

  <section class="cards">
    <div class="card adopt">
      <div class="icon">🐾</div>
      <h2>ADOPT</h2>
      <p>Meet our adorable, adoptable pets.</p>
    </div>

    <div class="card donate">
      <div class="icon">🎁</div>
      <h2>DONATE</h2>
      <p>Support our mission and help animals in need.</p>
    </div>

    <div class="card volunteer">
      <div class="icon">✋</div>
      <h2>VOLUNTEER</h2>
      <p>Join us and make a difference in the lives of animals.</p>
    </div>
  </section>

</body>
</html>
