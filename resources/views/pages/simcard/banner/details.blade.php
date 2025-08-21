@include('inc.header', ['load_vuejs' => false])
@include('inc.menu')
<div class="app-content content " id="app">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"></div>
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="alert-body">{{ $message }}</div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="content-body">
            <section class="app-user-list">
                <div class="card">
                    <div class="card-header"><h6 class="card-title"><?php echo $product_promotion->title; ?></h6></div>
                    <div class="card-body" :style="{'padding':'0'}">
                        <?php if(!empty($product_promotion->description)): ?>
                        <div class="row">
                            <div class="col-8"><img src="/<?php echo $product_promotion->file_name; ?>" class="mb-25 img-fluid rounded"></div>
                            <div class="col-4"><?php echo $product_promotion->description; ?></div>
                        </div>
                        <?php else: ?>
                            <img src="/<?php echo $product_promotion->file_name; ?>" class="mb-25 img-fluid rounded">
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@include('inc.footer', ['load_datatable_scripts' => false])
