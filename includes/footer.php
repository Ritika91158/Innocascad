<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>About InnoCascade</h3>
            <p>A platform for student innovation and collaboration. Share your ideas, connect with others, and make a difference.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-section">
            <h3>Quick Links</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="explore.php">Explore Ideas</a></li>
                <li><a href="share_idea.php">Share Your Idea</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Sectors</h3>
            <ul>
                <li><a href="explore.php?sector=Hospitality">Hospitality</a></li>
                <li><a href="explore.php?sector=Financial">Financial Services</a></li>
                <li><a href="explore.php?sector=Entertainment">Entertainment</a></li>
                <li><a href="explore.php?sector=Retail">Retail</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h3>Contact Us</h3>
            <ul>
                <li><i class="fas fa-envelope"></i> raviranjankashyap7@gmail.com</li>
                <li><i class="fas fa-phone"></i> +1 234 567 890</li>
                <li><i class="fas fa-map-marker-alt"></i> India</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> InnoCascade. All rights reserved.</p>
    </div>
</footer>

<style>
footer {
    background: rgba(10, 10, 15, 0.97);
    padding: 4rem 2rem 1rem;
    position: relative;
    overflow: hidden;
    margin-top: 4rem;
}

footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI1IiBoZWlnaHQ9IjUiPgo8cmVjdCB3aWR0aD0iNSIgaGVpZ2h0PSI1IiBmaWxsPSIjMDAwIj48L3JlY3Q+CjxwYXRoIGQ9Ik0wIDVMNSAwWk02IDRMNCA2Wk0tMSAxTDEgLTFaIiBzdHJva2U9IiMzZjQwNDUiIHN0cm9rZS13aWR0aD0iMSI+PC9wYXRoPgo8L3N2Zz4=');
    opacity: 0.05;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

.footer-section {
    flex: 1;
    min-width: 250px;
    margin-bottom: 2rem;
    padding: 0 1rem;
}

.footer-section h3 {
    color: #fff;
    margin-bottom: 1.5rem;
    font-size: 1.3rem;
    position: relative;
    display: inline-block;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    bottom: -8px;
    left: 0;
    width: 30px;
    height: 2px;
    background: var(--primary-color);
}

.footer-section p {
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 1.5rem;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 0.8rem;
    color: rgba(255, 255, 255, 0.7);
}

.footer-section ul li i {
    margin-right: 0.5rem;
    color: var(--primary-color);
}

.footer-section ul li a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-section ul li a:hover {
    color: var(--primary-color);
    padding-left: 5px;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: #fff;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.social-links a:hover {
    background: linear-gradient(to right bottom, var(--primary-color), var(--secondary-color));
    transform: translateY(-3px);
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    margin-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.5);
    position: relative;
    z-index: 1;
}

@media (max-width: 768px) {
    .footer-section {
        min-width: 100%;
        text-align: center;
    }

    .footer-section h3::after {
        left: 50%;
        transform: translateX(-50%);
    }

    .social-links {
        justify-content: center;
    }
}
</style> 