<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="row">
            @if($professional->info->citizenship_front)
                <div class="col-md-4 img-container">
                    <strong>Citizenship Front Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->info->full_citizenship_front }}" data-fancybox="image">
                            <img src="{{ $professional->info->full_citizenship_front }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->info->citizenship_back)
                <div class="col-md-4 img-container">
                    <strong>Citizenship Back Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->info->full_citizenship_back }}" data-fancybox="image">
                            <img src="{{ $professional->info->full_citizenship_back }}">
                        </a>
                    </div>
                </div>
            @endif
            @if($professional->info->signature_image)
                <div class="col-md-4 img-container">
                    <strong>Signature Image</strong>
                    <div class="document-image">
                        <a href="{{ $professional->info->full_signature_image }}" data-fancybox="image">
                            <img src="{{ $professional->info->full_signature_image }}">
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>