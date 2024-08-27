<section class="ptable-header-section py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4">
                <div class="ptable-header-left">
                    <h3 class="title mb-0">{{ __($pageTitle) }}</h3>
                    @stack('subtitle')
                </div>
            </div>
            <div class="col-md-8">
                <div class="ptable-header-right">
                    @stack('breadcrumb-plugins')
                </div>
            </div>
        </div>
    </div>
</section>
