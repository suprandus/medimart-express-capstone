@extends('frontend.layouts.master')
@section('title')
{{$settings->site_name}}
@endsection

@section('content')

<!--============================
        BANNER PART 2 START
    ==============================-->
@include('frontend.home.sections.banner-slider')
<!--============================
        BANNER PART 2 END
    ==============================-->


<!--============================
        FLASH SELL START
    ==============================-->
@include('frontend.home.sections.flash-sale')
<!--============================
        FLASH SELL END
    ==============================-->


<!--============================
       MONTHLY TOP PRODUCT START
    ==============================-->
@include('frontend.home.sections.top-category-product')
<!--============================
       MONTHLY TOP PRODUCT END
    ==============================-->


<!--============================
        BRAND SLIDER START
    ==============================-->
@include('frontend.home.sections.brand-slider')
<!--============================
        BRAND SLIDER END
    ==============================-->


<!--============================
        SINGLE BANNER START
    ==============================-->
@include('frontend.home.sections.single-banner')
<!--============================
        SINGLE BANNER END
    ==============================-->


<!--============================
        HOT DEALS START
    ==============================-->
@include('frontend.home.sections.hot-deals')
<!--============================
        HOT DEALS END
    ==============================-->


<!--============================
        ELECTRONIC PART START
    ==============================-->
@include('frontend.home.sections.category-product-slider-one')
<!--============================
        ELECTRONIC PART END
    ==============================-->


<!--============================
        ELECTRONIC PART START
    ==============================-->
@include('frontend.home.sections.category-product-slider-two')

<!--============================
        ELECTRONIC PART END
    ==============================-->


<!--============================
        LARGE BANNER  START
    ==============================-->
@include('frontend.home.sections.large-banner')

<!--============================
        LARGE BANNER  END
    ==============================-->


<!--============================
        WEEKLY BEST ITEM START
    ==============================-->
@include('frontend.home.sections.weekly-best-item')
<!--============================
        WEEKLY BEST ITEM END
    ==============================-->


<!--============================
      HOME SERVICES START
    ==============================-->
@include('frontend.home.sections.services')
<!--============================
        HOME SERVICES END
    ==============================-->

<!--============================
       CHATBOT NI LAWRENCE START
    ==============================-->
{{-- <script type="text/javascript">
    (function(d, t) {
            var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
            v.onload = function() {
              window.voiceflow.chat.load({
                verify: { projectID: '{{ $chatbotSettings->project_id }}' },
                url: '{{ $chatbotSettings->url }}',
                versionID: '{{ $chatbotSettings->version_id }}',
              });
            }
            v.src = "https://cdn.voiceflow.com/widget/bundle.mjs"; v.type = "text/javascript"; s.parentNode.insertBefore(v, s);
        })(document, 'script');
</script> --}}
<!--============================
        CHATBOT NI LAWRENCE END
    ==============================-->

<!--============================
        HOME BLOGS START
    ==============================-->
{{-- @include('frontend.home.sections.blog') --}}
<!--============================
        HOME BLOGS END
    ==============================-->

@endsection