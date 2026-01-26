<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>End Animal Cruelty</title>
    <style>
        /* General Styles for Banner Section */
        body {
          font-family: 'Segoe UI', sans-serif;
          margin: 0;
          padding: 0;
          background-color: #f1f1f1;
        }

        .banner-section {
          width: 100%;
          background-color: #f8f8f8;
          padding: 40px 20px;
          display: flex;
          justify-content: center;
          align-items: center;
        }

        .banner {
          display: flex;
          justify-content: space-between;
          align-items: center;
          max-width: 1200px;
          width: 100%;
          background-color: #ffffff;
          padding: 30px;
          box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
          border-radius: 10px;
        }

        /* Left side with blurred image */
        .banner-left {
          flex: 1;
          position: relative;
          background: url('crueltyimage.jpg') center/cover no-repeat;
          filter: blur(5px);  /* Apply blur effect */
          height: 100%; /* Ensures full height for the background */
          border-radius: 10px;
          overflow: hidden;
        }

        .banner-left::after {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: rgba(0, 0, 0, 0.5); /* Dark overlay */
          border-radius: 10px;
        }

        /* Right side content */
        .banner-right {
          flex: 2;
          padding: 0 30px;
        }

        h2 {
          font-size: 2.5em;
          font-weight: bold;
          color: #d9534f;
          margin-bottom: 20px;
        }

        p {
          font-size: 1.2em;
          color: #555;
          margin-bottom: 20px;
          line-height: 1.5;
        }

        .donate-btn {
          font-size: 1.2em;
          background-color: #d9534f;
          color: white;
          padding: 15px 25px;
          text-decoration: none;
          border-radius: 5px;
          transition: background-color 0.3s ease;
        }

        .donate-btn:hover {
          background-color: #c9302c;
        }

        @media (max-width: 768px) {
          .banner {
            flex-direction: column;
            text-align: center;
          }

          .banner-left,
          .banner-right {
            flex: 1;
            width: 100%;
            padding: 10px;
          }

          h2 {
            font-size: 2em;
          }

          p {
            font-size: 1em;
          }
        }
    </style>
</head>
<body>

    <section class="banner-section">
        <div class="banner">
            <div class="banner-left">
                <div class="content-warning">
                    <!-- You can add any image or text here if needed -->
                </div>
            </div>
            <div class="banner-right">
                <h2>LET’S PUT AN END TO ANIMAL CRUELTY</h2>
                <p>Every year thousands of animals suffer from neglect, cruelty, and abuse. With your help, we can end their misery.</p>
                <a href="cruelty.html" class="donate-btn">Report Cruelty</a>
            </div>
        </div>
    </section>

</body>
</html>
