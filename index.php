<?php
session_start();
// Include database configuration
require_once 'config/database.php';

if (isset($_POST['submit'])) {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $number = sanitize_input($_POST['number']);
    $date = sanitize_input($_POST['date']);

    // Validation
    if (empty($name) || empty($email) || empty($number) || empty($date)) {
        $message = 'Please fill in all fields!';
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address!';
        $message_type = 'error';
    } else {
        try {
            // Use prepared statement to prevent SQL injection
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $stmt = $pdo->prepare("INSERT INTO appointments (user_id, name, email, number, date, status) VALUES (:user_id, :name, :email, :number, :date, 'pending')");
            $result = $stmt->execute([
                'user_id' => $user_id,
                'name' => $name,
                'email' => $email,
                'number' => $number,
                'date' => $date
            ]);

            if ($result) {
                $message = 'Appointment request submitted successfully! We will contact you soon.';
                $message_type = 'success';
            } else {
                $message = 'Failed to book appointment. Please try again.';
                $message_type = 'error';
            }
        } catch (PDOException $e) {
            error_log("Appointment Error: " . $e->getMessage());
            $message = 'An error occurred. Please try again later.';
            $message_type = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dentist Website</title>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- bootstrap link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <!-- header -->
    <section class="header">
        <a href="#home" class="logo">dental<span>Care.</span></a>

        <nav class="nav">
            <a href="#home">home</a>
            <a href="#about">about</a>
            <a href="#services">services</a>
            <a href="#reviews">reviews</a>
            <a href="#contact">contact</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" style="color: var(--blue);">login</a>
                <a href="register.php" style="color: var(--blue);">register</a>
            <?php else: ?>
                <span style="font-size: 1.7rem; color: var(--blue); margin: 0 1rem;"><i class="fas fa-user"></i> <?php echo explode(' ', $_SESSION['user_name'])[0]; ?></span>
                <a href="logout.php" style="color: var(--red);">logout</a>
            <?php endif; ?>
        </nav>

        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="#contact" class="link-btn">make appointment</a>
        <?php else: ?>
            <a href="login.php" class="link-btn">login to book</a>
        <?php endif; ?>

        <i id="menu-btn" class="fas fa-bars"></i>
    </section>

    <!-- home -->
    <section class="home" id="home">
        <div class="container">
            <div class="row min-vh-100 align-items-center">
                <div class="content text-start">
                    <h3>Let us brighten your smile</h3>
                    <p>At our clinic, we are dedicated to providing exceptional dental care tailored to your unique needs. Whether it’s a routine check-up or a cosmetic enhancement, our team is here to ensure your smile stays healthy and radiant. Trust us to deliver personalized service with a gentle touch.</p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="#contact" class="link-btn">make appointment</a>
                    <?php else: ?>
                        <a href="login.php" class="link-btn">login to book</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- about -->
    <section class="about reveal" id="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 image">
                    <img src="images/about-img.jpg" alt="about image" class="w-100 mb-5 mb-md-0" style="border-radius: 2rem; box-shadow: var(--box-shadow);">
                </div>

                <div class="col-md-4 content">
                    <span>About us</span>
                    <h3>True Healthcare for Your Family</h3>
                    <p>We are a trusted healthcare provider committed to delivering compassionate and comprehensive medical care for your entire family. With a team of experienced professionals and a patient-centered approach, we strive to create a welcoming environment where your health and well-being are our top priorities.</p>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="#contact" class="link-btn">make appointment</a>
                    <?php else: ?>
                        <a href="login.php" class="link-btn">login to book</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- services -->
    <section class="services reveal" id="services">
        <h1 class="heading">Our services</h1>

        <div class="box-container container">
            <div class="box">
                <img src="images/icon-1.png" alt="">
                <h3>Aligment Specialist</h3>
                <p>We correct misaligned teeth using braces or clear aligners. Get a straighter, healthier smile tailored just for you.</p>
            </div>

            <div class="box">
                <img src="images/icon-2.png" alt="">
                <h3>Cosmetic Dentistry</h3>
                <p>Enhance your smile with whitening, veneers or complete makeovers. Our treatments deliver beautiful, natural-looking results.</p>
            </div>

            <div class="box">
                <img src="images/icon-3.png" alt="">
                <h3>Oral Hygiene Experts</h3>
                <p>We provide thorough cleanings and preventive dental care. Keep your teeth and gums healthy for life with our expertise.</p>
            </div>

            <div class="box">
                <img src="images/icon-4.png" alt="">
                <h3>Root Canal Specialist</h3>
                <p>Our pain-free root canals save infected teeth. We use gentle techniques to relieve discomfort quickly.</p>
            </div>

            <div class="box">
                <img src="images/icon-5.png" alt="">
                <h3>Live Dental Advisory</h3>
                <p>Get instant dental advice through virtual consultations. Our experts are ready to help from anywhere.</p>
            </div>

            <div class="box">
                <img src="images/icon-6.png" alt="">
                <h3>Cavity Inspection</h3>
                <p>We detect cavities early with advanced technology. Enjoy minimally invasive treatments for better oral health.</p>
            </div>
        </div>
    </section>



    <!-- process -->
    <section class="process reveal">
        <h1 class="heading">Work process</h1>

        <div class="box-container container">
            <div class="box">
                <img src="images/process-1.jpg" alt="">
                <h3>Cosmetic Dentistry</h3>
                <p>We use whitening, veneers, and bonding techniques to give you a brighter, more confident smile you'll love showing off.</p>
            </div>

            <div class="box">
                <img src="images/process-2.jpg" alt="">
                <h3>Pediatric Dentistry</h3>
                <p>Gentle and friendly dental care designed specifically for children, making every visit stress-free and fun.</p>
            </div>

            <div class="box">
                <img src="images/process-3.jpg" alt="">
                <h3>Dental Implants</h3>
                <p>Permanent, natural-looking tooth replacements that restore your ability to eat, speak, and smile with confidence.</p>
            </div>
        </div>
    </section>


    <!-- reviews -->
    <section class="reviews reveal" id="reviews">
        <h1 class="heading">Satisfied clients</h1>

        <div class="box-container container">
            <div class="box">
                <img src="images/pic-1.jpg" alt="">
                <p>“I had a wonderful experience at DentalCare. The staff was incredibly professional and kind, and they made sure I was comfortable throughout the entire procedure. I’ve never felt more at ease during a dental visit!”</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Emily Carter</h3>
                <span>Satisfied client</span>
            </div>

            <div class="box">
                <img src="images/pic-2.jpg" alt="">
                <p>“From start to finish, the service was exceptional. The dentist explained everything clearly, and the results exceeded my expectations. Highly recommend this clinic to anyone looking for top-quality dental care.”</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h3>Michael Rodriguez</h3>
                <span>Satisfied client</span>
            </div>

            <div class="box">
                <img src="images/pic-3.jpg" alt="">
                <p>“I was nervous at first, but the team at DentalCare immediately put me at ease. The clinic is clean and modern, and the treatment was painless. I’ll definitely be coming back for my regular checkups.”</p>
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <h3>Sarah Nguyen</h3>
                <span>Satisfied client</span>
            </div>
        </div>
    </section>


    <!-- contact -->
    <section class="contact reveal" id="contact">
        <?php if (isset($_SESSION['user_id'])): ?>
            <h1 class="heading">make appointment</h1>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <?php
                if (isset($message)) {
                    $class = isset($message_type) && $message_type === 'error' ? 'message error' : 'message success';
                    echo '<p class="' . $class . '">' . $message . '</p>';
                }
                ?>
                <span>Your name :</span>
                <input type="text" name="name" placeholder="enter your name" class="box" required value="<?php echo $_SESSION['user_name']; ?>">
                <span>Your email :</span>
                <input type="email" name="email" placeholder="enter your email" class="box" required value="<?php echo $_SESSION['user_email']; ?>">
                <span>Your number :</span>
                <input type="number" name="number" placeholder="enter your number" class="box" required value="<?php echo $_SESSION['user_number']; ?>">
                <span>Appointment date :</span>
                <input type="datetime-local" name="date" class="box" required>
                <input type="submit" value="make appointment" name="submit" class="link-btn">
            </form>
        <?php else: ?>
            <div style="text-align: center; padding: 2rem;">
                <h1 class="heading">make appointment</h1>
                <p style="font-size: 2rem; color: #666; margin-bottom: 2rem;">Please login to make an appointment.</p>
                <a href="login.php" class="link-btn">Login Now</a>
            </div>
        <?php endif; ?>
    </section>

    <!-- footer -->
    <section class="footer">
        <div class="box-container container">
            <div class="box">
                <i class="fas fa-phone"></i>
                <h3>Phone number</h3>
                <p>+94-71-060-9086</p>
                <p>+94-71-532-0896</p>
            </div>

            <div class="box">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Our address</h3>
                <p>272, pragathipura, madiwela, kotte</p>
            </div>

            <div class="box">
                <i class="fas fa-clock"></i>
                <h3>Open times</h3>
                <p>07:00AM to 10:00PM in weekdays</p>
                <p>09:00AM to 05:00PM in weekends</p>
            </div>

            <div class="box">
                <i class="fas fa-envelope"></i>
                <h3>Email address</h3>
                <p>chaminduodi@gmail.com</p>
                <p>chamindukumarasinghe22@gmail.com</p>
            </div>
        </div>

        <div class="credit">©dentalCare. | All rights reserved!</div>
    </section>

    <!-- custom js file link -->
    <script src="js/script.js"></script>
</body>

</html>