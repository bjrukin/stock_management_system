@extends('layout.professional')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <!-- Main content -->
        <div class="content">
            <div class="button ml-5 mt-2">
                <button onclick="printDiv()" class="btn btn-primary">Print Id Card</button>
            </div>
            <div class="row mt-5">

                <div class="id-card-content"
                    style="width:400px; height : 250px; padding:10px; background:white; border:1px solid black;"
                    id="printContent">
                    <div class="header" style="text-align: center; width:100%;">
                        <div class="logo" style="width: 15%;float:left;">
                            <img class="img-responsive" width="60" height="60" style="margin-top:10px;"
                                src="https://nhpc.gov.np/beta//assets/img/nhpc_logo.jpg">
                        </div>
                        <div class="logo" style="width: 70%; float:left; line-height:1.0;">
                            <span style="font-size: 14px;">Nepal Health Professional Council </span><br>
                            <span style="font-size: 11px;">Established by Nepal Health Professional Council
                                Act.2053</span><br>
                            <span style="font-size: 10px;">Bansbari, Kathmandu, Tel: 4373118</span>
                            <div
                                style="background:red; margin-top:6px; font-size:12px; padding:3px; border-radius: 5px; margin-left:28%; margin-right:28%;">
                                <span style="font-weight: bold; color:white;">IDENTITY CARD</span>
                            </div>
                        </div>
                        <div class="logo" style="width: 15%;float:left; justify-content:center; margin-top:10px;">
                            {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(60)->generate(
                             'Name :- ' . $certificate?->professional->name . ' Licence Number : -' . $certificate?->cert_registration_number,) !!}
                        </div>
                    </div>
                    <div class="body" style="width:100%; margin-top:20px;">
                        <div class="logo" style="line-height: 1.8; width: 77%; float:left; font-size:11px;">
                            <span>Reg No : <span style="font-weight:700;"> {{ $certificate?->cert_registration_number }}
                                </span> </span><br>
                            <span>Name : <span style="font-weight:700; text-transform: capitalize;">
                                    {{ $certificate?->professional->name }} </span></span></br>
                            <span>Level : <span style="font-weight:700; text-transform: capitalize;">
                                    {{ $certificate?->level->name }}
                                </span></span></br>
                            <span>Discipline (Sub): <span style="font-weight:700; text-transform: capitalize;">
                                    {{ $certificate?->qualification }} </span></span></br>
                            <span>Valid Up To : {{ $certificate?->valid_till }}</span></br>
                            <span>Signature : ....................................................</span></br>

                        </div>
                        {{-- <div class="logo" style="width: 20%;float:left; text-align:center; font-size:11px;">
                            <span style="z-index: 2000;"> <img src="https://nhpc.gov.np/beta//assets/img/nhpc_logo.jpg"
                                    class="logo" width="25px" height="25px"
                                    style="position: absolute; margin-bottom: -15px; margin-top:10px; z-index: 10000;"></span></br>
                            <img class="img-responsive" width="90" height="90" src={{
                                $certificate?->professional->info->getFullProfilePictureAttribute() }}></br>
                            <span style="z-index: 2000;                                        <img src=" {{
                                $certificate?->professional->info->full_signature_image }}"
                                class="signature" width="50px" height="25px"
                                style="position: absolute; margin-top: -20px; z-index: 10000; margin-left:
                                -15px;"></span></br>
                            <span>Registrar</span>
                        </div> --}}
                        <div class="logo" style="width: 23%;float:left; text-align:center; font-size:11px;">
                            <div class="img-container">
                                <div class="profile-picture" style="position:relative;margin:10px 0 20px;">
                                    <img class="img-responsive" width="90" height="90"
                                        src="{{ $certificate?->professional->info->getFullProfilePictureAttribute() }}">
                                    <div class="nhpc_logo">
                                        <img src="{{ asset('storage/app/public/nhpc_logo.png') }}" class="logo" width="25px"
                                            height="25px" style="position: absolute;bottom:75px; z-index: 10000;">
                                    </div>
                                    <div class="signature_image">
                                        <img src="{{ $certificate?->professional->info->full_signature_image }}"
                                            class="signature" width="70px" height="40px"
                                            style="position: absolute;left:10px;top:70px;">
                                    </div>
                                </div>
                                <span>Registrar</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('footer-scripts')
    @include('professional.icard.js.index')
@endpush