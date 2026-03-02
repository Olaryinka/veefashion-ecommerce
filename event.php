<?php
require_once('./layout/header.php');
?>

<!-- Marquee section -->
<div class="marquee" data-text="👜 New Arrivals | 🎉 Fashion Week Starts Next Month | 💄 20% Discount on All Dresses!"></div>

<!-- Event section -->
<section class="event-section">

    <h3 class="event-heading">UpComing Events</h3>
    <p class="event-subheading">
        Get ready for a show full of rhythm, costumes, and unforgettable performances.<br>
        Where style meets innovation—don’t miss what's next in fashion.
    </p>

    <div class="event-container">

        <!-- Carnival Event -->
        <div class="carnival-video_wrapper">
            <video autoplay muted loop playsinline class="carnival-video">
                <source src="<?= BASE_URL ?>/assets/media/fashion-video-3.mp4" type="video/mp4">
            </video>
            <div class="carnival-content">
                <span>Carnival Show</span>
                <p>March 15, 2026</p>
            </div>
        </div>

        <!-- Fashion Show -->
        <div class="fashion-video_wrapper">
            <video autoplay muted loop playsinline class="fashion-video">
                <source src="<?= BASE_URL ?>/assets/media/fashion-video-4.mp4" type="video/mp4">
            </video>
            <div class="fashion-content">
                <span>Fashion Show</span>
                <p>March 20, 2026</p>
            </div>
        </div>

    </div>

</section>

<?php
require_once('./layout/footer.php');
?>