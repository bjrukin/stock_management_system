@if($professional->info && $professional->info->first_name)
<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            @if ($professional->info->citizenship_front)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Citizenship Front Image</label>
                      <div><a href="{{ $professional->info->full_citizenship_front }}"
                            data-fancybox="image"><img src="{{ $professional->info->full_citizenship_front }}"></a>
                    </div>
                </div>

            </div>
            @endif
            @if ($professional->info->citizenship_back)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Citizenship Back Image</label>
                      <div><a href="{{ $professional->info->full_citizenship_back }}"
                            data-fancybox="image"><img src="{{ $professional->info->full_citizenship_back }}"></a>
                    </div>
                </div>

            </div>
            @endif

            @if ($professional->info->signature_image)
            <div class="col-md-4 mb-3 text-center">
                <div class="tab-content"> <label class="form-label">Signature Image</label>
                      <div><a href="{{ $professional->info->full_signature_image }}"
                            data-fancybox="image"><img src="{{ $professional->info->full_signature_image }}"></a>
                    </div>
                </div>

            </div>
            @endif
        </div>
    </div>
</div>
@endif