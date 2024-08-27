@php
    $overviewElements = getContent('overview.element')
@endphp
<section class="overview-section">
    <div class="container">
      <div class="row">
          @foreach ($overviewElements as $overview)
            <div class="col-lg-3 col-sm-6 overview-item">
                <div class="overview-card text-center">
                <div class="overview-card__icon">
                    @php echo @$overview->data_values->icon @endphp
                </div>
                <div class="overview-card__content">
                    <h6 class="title">{{__(@$overview->data_values->details)}}</h6>
                </div>
                </div>
            </div>
          @endforeach
      </div>
    </div>
  </section>
