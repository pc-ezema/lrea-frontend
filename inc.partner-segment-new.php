<div class="py-5"> 
    <h1 class="partner-section-title">Partner with Us</h1>    
    <div class="partner-section">
        <div class="card partner-card" onclick="window.location.href='<?php echo $link_root_path; ?>subscriptions'" style="cursor:pointer;">
            <h3>Membership Program</h3>
            <p>Join the London Real Estate Academy community and enjoy exclusive access to expert resources, mentorship, and professional growth opportunities.</p>
            <button class="partner-btn">Become a Member</button>
        </div>

        <div class="card partner-card">
            <h3>Affiliates</h3>
            <p>Promote LREA and earn commissions. Perfect for marketers, creators, and sales pros.</p>
            <button class="partner-btn openPartnerModal">Become an Affiliate</button>
        </div>

        <div class="card partner-card">
            <h3>Industry Partners</h3>
            <p>Collaborate with LREA to provide training, workshops, or resources. Ideal for developers, real estate firms, and service providers.</p>
            <button class="partner-btn openPartnerModal">Become an Industry Partner</button>
        </div>

        <div class="card partner-card">
            <h3>Ambassadors</h3>
            <p>Promote LREA on social media and earn cash. Perfect if you’re active on LinkedIn, YouTube, TikTok, or X.</p>
            <button class="partner-btn openPartnerModal">Become an Ambassador</button>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="partner-modal" id="myPartnerModal">
    <div class="partner-modal-content">
        <span class="partner-close">&times;</span>
        <h2>Submit Your Details</h2>
        <p id="formMessage" style="margin-top:10px; font-size:14px;"></p>
        <form id="partnerForm">
            <input type="text" id="role" name="role" placeholder="Selected Role" readonly required>
            <input type="text" id="name" name="name" placeholder="Your Name" required>
            <input type="text" id="number" name="number" placeholder="Your Phone Number" required>
            <input type="email" id="email" name="email" placeholder="Your Email" required>
            <button type="submit" class="partnerBtn">Send</button>
        </form>
    </div>
</div>

<style>
    .partner-section-title {
        text-align: center;
        margin: 2rem 0px;
        color: #000; /* matches card headings */
    }
    .partner-section {
        display: flex;
        gap: 10px;
        justify-content: center;
        text-align: center;
        flex-wrap: wrap;
        margin: 3rem 0px;
    }

    .partner-card {
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        width: 300px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
        justify-content: space-between !important;
    }

    .partner-card:hover {
        transform: translateY(-5px);
    }

    .partner-card h3 {
        margin-bottom: 10px;
        color: #003366;
    }

    .partner-card p {
        font-size: 14px;
        color: #444;
        margin-bottom: 20px;
    }

    .partner-btn,
    .partnerBtn {
        background: #ea383e;
        color: #fff;
        padding: 10px 20px;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        transition: background 0.3s;
        width: 100%;
    }

    .partner-btn:hover,
    .partnerBtn:hover {
        background: #ea383e;
    }

    /* Modal */
    .partner-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .partner-modal-content {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        width: 400px;
        position: relative;
    }

    .partner-modal-content h2 {
        margin-top: 0;
    }

    .partner-modal-content input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    .partner-close {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 22px;
        cursor: pointer;
    }
</style>

<script>
    // Open modal and set role dynamically
    document.querySelectorAll('.openPartnerModal').forEach(partnerBtn => {
        partnerBtn.addEventListener('click', (e) => {
            const selectedRole = e.target.innerText; // Button text
            document.getElementById('role').value = selectedRole; // Fill role input
            document.getElementById('myPartnerModal').style.display = 'flex';
        });
    });

    // Close modal
    document.querySelector('.partner-close').addEventListener('click', () => {
        document.getElementById('myPartnerModal').style.display = 'none';
    });

    // Submit form with AJAX
    document.getElementById('partnerForm').addEventListener('submit', function(e) {
        e.preventDefault();
    
        const form = this;
        const btn = form.querySelector('.partnerBtn');
        const msg = document.getElementById('formMessage');
        const formData = new FormData(form);
    
        // Set loading state
        btn.disabled = true;
        btn.innerText = "Sending... ⏳";
    
        fetch("partner_submit.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(response => {
                if (response.trim() === "success") {
                    msg.style.color = "green";
                    msg.innerText = "🎉 Thank you! Your request has been submitted successfully.";
                    form.reset();
                } else {
                    msg.style.color = "red";
                    msg.innerText = "⚠️ Oops! Something went wrong. Please try again.";
                }
            })
            .catch(() => {
                alert("❌ Network error. Please check your connection and try again.");
            })
            .finally(() => {
                // Restore button
                btn.disabled = false;
                btn.innerText = "Send";
            });
    });
</script>