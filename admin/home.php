<h1 class="text-light fade-in">Welcome to <?php echo $_settings->info('name') ?></h1>
<hr class="border-light">
<div class="row">
    <div class="col-12 col-sm-6 col-md-3 fade-in-up stagger-1">
        <div class="info-box info-box-glass">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Partnered Companies</span>
            <span class="info-box-number text-right">
                <?php echo $conn->query("SELECT * FROM `company_list` where status =1")->num_rows; ?>
            </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 fade-in-up stagger-2">
        <div class="info-box info-box-glass">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-friends"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Clients</span>
            <span class="info-box-number text-right">
                <?php echo $conn->query("SELECT * FROM `users` where `type` =2")->num_rows; ?>
            </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 fade-in-up stagger-3">
        <div class="info-box info-box-glass">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-credit-card"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Transactions</span>
            <span class="info-box-number text-right">
                <?php echo $conn->query("SELECT * FROM `transaction_list`")->num_rows; ?>
            </span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3 fade-in-up stagger-4">
        <div class="info-box info-box-glass">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
            <span class="info-box-text">Total Revenue</span>
            <span class="info-box-number text-right">
                <?php 
                    $rev = $conn->query("SELECT SUM(payable_amount) as total FROM `transaction_list` WHERE status = 'completed' OR status IS NULL")->fetch_assoc();
                    echo number_format($rev['total'] ?? 0, 2);
                ?>
            </span>
            </div>
        </div>
    </div>
</div>
