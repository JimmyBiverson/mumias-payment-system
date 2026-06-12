
<style>
    #top-header{
        height:70vh;
        
    }
    #top-header *{
        text-shadow: 3px 2px #000000b0;
    }
    #top-header .title{
        font-size:5em;
        text-shadow:4px 4px #000000f5;
    }
    #top-header:before{
        content:'';
        position:absolute;
        height:80vh;
        width: calc(100%);
        top:0;
        left:0;
        background-image:url('<?php echo validate_image($_settings->info('cover')) ?>') !important;
        background-size:cover;
        background-repeat:no-repeat;
        background-position:center center;
        z-index: 0;
        filter:brightness(.95)
    }
    .company-name {
        font-size: 1.35rem;
        font-variant: all-petite-caps;
        font-family: monospace;
        font-weight: 600;
        color: #b5b5b5;
    }
    img.company-logo {
        transition: transform .02s ease-in;
    }
    img.company-logo:hover {
        transform: scale(1.02);
    }
        /* NEW: animated ticker / slider */
        .ticker {
            overflow: hidden;
            position: relative;
            background: linear-gradient(90deg, rgba(0,123,255,0.12), rgba(72,187,120,0.06));
            border-radius: 8px;
            padding: 8px 0;
            margin-top: 1rem;
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }
        .ticker__wrap{
            display:flex;
            white-space:nowrap;
            will-change:transform;
            animation: ticker 18s linear infinite;
            align-items:center;
            gap: 2.5rem;
            padding-left: 100%;
        }
        .ticker__item{
            display:inline-flex;
            align-items:center;
            gap:.8rem;
            font-weight:700;
            color: #fff;
            text-transform:uppercase;
            letter-spacing: .08em;
            font-size:1.1rem;
            padding: .25rem 1rem;
            background: linear-gradient(90deg, rgba(0,0,0,0.25), rgba(255,255,255,0.03));
            border-radius: 30px;
            backdrop-filter: blur(4px);
        }
        @keyframes ticker{
            0%{transform: translateX(0);} 
            100%{transform: translateX(-50%);} 
        }

        /* Hero illustration styling */
        .hero-illustration{
            border-radius: 20px;
            padding: 1rem;
            background: linear-gradient(135deg, rgba(23,162,184,0.12), rgba(0,123,255,0.06));
            display:inline-block;
            box-shadow: 0 18px 40px rgba(2,6,23,0.35);
            transform: translateY(-6px);
            transition: transform .25s ease, box-shadow .25s ease;
        }
        .hero-illustration img{max-width:100%;height:auto;border-radius:12px;display:block}

        /* NEW: feature cards */
        .feature-card{
            background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
            border-radius:12px;
            padding:1.6rem;
            transition: transform .25s ease, box-shadow .25s ease;
            color: #f1f1f1;
            min-height:160px;
        }
        .feature-card:hover{transform: translateY(-6px); box-shadow: 0 10px 30px rgba(0,0,0,0.25)}
        .feature-card .fa-3x{color: #17a2b8}

        /* responsive tweaks */
        @media (max-width:767px){
            #top-header{height:50vh}
            #top-header .title{font-size:2.2rem}
            .ticker__wrap{animation-duration:10s}
            .ticker__item{font-size:.95rem}
            /* place ticker absolutely inside header to avoid extra vertical space */
            .position-relative > .ticker{position:absolute;left:8px;right:8px;bottom:8px;padding:6px 0;margin-top:0}
            .position-relative > .ticker .ticker__wrap{padding-left: 50% ;gap:1.2rem;}
            .position-relative > .ticker .ticker__item{font-size:.92rem;padding:.2rem .6rem}
            #top-header{overflow:hidden}
        }
</style>
<header id="top-header" class="d-flex justify-content-center align-items-end py-3 px-5">
    <div class="position-relative" style="z-index: 1;">
    <div class="mb-5 pb-5">
        <h1 class='text-center title'>Welcome to <?php echo $_settings->info('name') ?></h1>
    </div>
        <!-- Animated ticker below the title -->
        <div class="ticker container">
            <div class="ticker__wrap">
                <div class="ticker__item"><i class="fa fa-lock fa-lg"></i> Secure Online Pay</div>
                <div class="ticker__item"><i class="fa fa-credit-card fa-lg"></i> Fast Settlements</div>
                <div class="ticker__item"><i class="fa fa-shield-alt fa-lg"></i> PCI Compliant</div>
                <div class="ticker__item"><i class="fa fa-globe fa-lg"></i> Multi-Gateway Support</div>
                <div class="ticker__item"><i class="fa fa-phone fa-lg"></i> 24/7 Support</div>
                <div class="ticker__item"><i class="fa fa-users fa-lg"></i> Trusted Partners</div>
                <div class="ticker__item"><i class="fa fa-chart-line fa-lg"></i> Real-time Reporting</div>
                <div class="ticker__item"><i class="fa fa-mobile-alt fa-lg"></i> Mobile Friendly</div>
                <div class="ticker__item"><i class="fa fa-key fa-lg"></i> Tokenization</div>
                <div class="ticker__item"><i class="fa fa-exchange-alt fa-lg"></i> Flexible Routing</div>
                <div class="ticker__item"><i class="fa fa-file-invoice-dollar fa-lg"></i> Automated Invoicing</div>
                <div class="ticker__item"><i class="fa fa-shield-virus fa-lg"></i> Fraud Protection</div>
                <!-- duplicate sequence to ensure smooth continuous loop -->
                <div class="ticker__item"><i class="fa fa-lock fa-lg"></i> Secure Online Pay</div>
                <div class="ticker__item"><i class="fa fa-credit-card fa-lg"></i> Fast Settlements</div>
                <div class="ticker__item"><i class="fa fa-shield-alt fa-lg"></i> PCI Compliant</div>
                <div class="ticker__item"><i class="fa fa-globe fa-lg"></i> Multi-Gateway Support</div>
                <div class="ticker__item"><i class="fa fa-phone fa-lg"></i> 24/7 Support</div>
                <div class="ticker__item"><i class="fa fa-users fa-lg"></i> Trusted Partners</div>
            </div>
        </div>
    </div>
</header>
<section class="py-4">
    <div class="container">
        <?php echo is_file('welcome_message.html') ? file_get_contents('welcome_message.html') : "Welcome Content is Empty" ?>
    </div>
<!-- New feature area -->
<section class="py-5">
    <div class="container">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h2 class="display-5">Payments made simple, secure and fast</h2>
                <p class="text-muted">Accept payments online with confidence. Our platform supports multiple payment gateways, secure tokenization, and easy reconciliation so you can focus on growing your business.</p>
                <a href="login.php" class="btn btn-info btn-lg mt-2">Make a Payment</a>
            </div>
            <div class="col-md-6 text-center d-none d-md-block">
                <div class="hero-illustration mx-auto" style="max-width:360px;">
                    <img src="assets/img/payment-illustration.svg" alt="payments" />
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-md-4">
                <div class="feature-card text-center">
                    <div class="mb-3"><i class="fa fa-lock fa-3x"></i></div>
                    <h5>Secure Payments</h5>
                    <p class="small text-muted">Encrypted transactions and robust fraud detection to keep payments safe.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="feature-card text-center">
                    <div class="mb-3"><i class="fa fa-bolt fa-3x"></i></div>
                    <h5>Fast Settlements</h5>
                    <p class="small text-muted">Quick processing and clear reporting so you always know your status.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <div class="feature-card text-center">
                    <div class="mb-3"><i class="fa fa-cogs fa-3x"></i></div>
                    <h5>Easy Integration</h5>
                    <p class="small text-muted">Simple setup and multiple gateway options to suit your needs.</p>
                </div>
            </div>
        </div>
    </div>
</section>
</section>
<section class="py-4 bg-gradient-dark">
    <div class="container">
        <h2 class="text-center">Our Partners</h2>
        <center><hr class="border-primary" width="50px"></center>
        <div class="col-12">
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-xl-3 gx-2 gy-2 justify-content-center">
                <?php 
                $company = $conn->query("SELECT * FROM `company_list` where status = 1 order by `name` asc");
                while($row = $company->fetch_assoc()):
                ?>
                <div class="col">
                    <div class="m-2 w-100 d-flex flex-column justify-content-center align-items-center h-100 overflow-hidden">
                        <img src="<?php echo validate_image(is_file(base_app."uploads/company_logos/{$row['id']}.png") ? "uploads/company_logos/{$row['id']}.png?v=".(strtotime($row['date_updated'])) : $_settings->info('logo') ) ?>" alt="<?php echo $row['name'] ?> logo" class="company-logo mb-2">
                        <div class="company-name text-center"><?php echo $row['name'] ?></div>
                    </div>
                </div> 
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>