<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"
    />

    <title>Sales Drive | admin panel</title>
  </head>
  <body>
    <!--<main class="login">-->
    <!--  <div class="login__left" style="background:url({{ asset('admin/images/brand_commercial.png') }}); background-repeat: no-repeat;-->
    <!--  background-size: cover;-->
    <!--  background-position: center center;">-->
    <!--    {{-- <img src="{{ asset('admin/images/brand_commercial.png') }}"> --}}-->
    <!--  </div>-->
    <!--  <div class="login__right">-->
    <!--    <div class="login__block">-->
    <!--      <div class="logo__block">-->
    <!--        <img src="{{ asset('admin/images/logo.png') }}">-->
    <!--      </div>-->

    <!--      @if (Session::get('success'))<div class="alert alert-success">{{ Session::get('success') }}</div>@endif-->
    <!--      @if (Session::get('failure'))<div class="alert alert-danger">{{ Session::get('failure') }}</div>@endif-->

    <!--      <form method="POST" action="{{ route('admin.login.check') }}">-->
    <!--      @csrf-->
    <!--        <div class="form-floating mb-3">-->
    <!--          <input type="email" class="form-control" name="email" value="{{ old('email') }}" id="floatingInput" placeholder="name@example.com">-->
    <!--          <label for="floatingInput">Email address</label>-->
    <!--        </div>-->
    <!--        @error('email') <p class="small text-danger">{{ $message }}</p> @enderror-->

    <!--        <div class="form-floating mb-3">-->
    <!--          <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Password">-->
    <!--          <label for="floatingPassword">Password</label>-->
    <!--        </div>-->
    <!--        @error('password') <p class="small text-danger">{{ $message }}</p> @enderror-->

    <!--        {{-- <div class="row mb-3">-->
    <!--          <div class="col-6">-->
    <!--            <div class="form-check">-->
    <!--              <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">-->
    <!--              <label class="form-check-label" for="flexCheckDefault">-->
    <!--                Remember Me-->
    <!--              </label>-->
    <!--            </div>-->
    <!--          </div>-->
    <!--          <div class="col-6 text-end">-->
    <!--            <a href="{{ route('admin.forget.password.get') }}">Forgot Password?</a>-->
    <!--          </div>-->
    <!--        </div> --}}-->

    <!--        <div class="d-grid">-->
    <!--          <button type="submit" class="btn btn-lg btn-primary">Login</button>-->
    <!--        </div>-->
    <!--      </form>-->

    <!--      {{-- <div class="row mt-3">-->
    <!--          <div class="col-12 text-center">-->
    <!--            <a href="{{ url('/') }}">Back to homepage</a>-->
    <!--          </div>-->
    <!--        </div> --}}-->
    <!--    </div>-->
    <!--  </div>-->
    <!--</main>-->
    
    <div class="login-page">
        <div class="container">
            <div class="row align-items-center">
                <!--<div class="col-12 col-md-6">-->
                <!--    <div class="login-slider">-->
                <!--        <div class="swiper-rel">-->
                <!--            <div class="swiper swiper-login-slider">-->
                <!--                <div class="swiper-wrapper">-->
                <!--                    <div class="swiper-slide">-->
                <!--                        <div class="login-img">-->
                <!--                            <img src="{{ asset('admin/images/our-brand-varun.jpg') }}" class="img-fluid" />-->
                <!--                        </div>-->
                <!--                    </div>-->
                                    
                <!--                    <div class="swiper-slide">-->
                <!--                        <div class="login-img">-->
                <!--                            <img src="{{ asset('admin/images/jacqueline.jpg') }}" class="img-fluid" />-->
                <!--                        </div>-->
                <!--                    </div>-->
                                    
                <!--                    <div class="swiper-slide">-->
                <!--                        <div class="login-img">-->
                <!--                            <img src="{{ asset('admin/images/SOURAV.jpg') }}" class="img-fluid" />-->
                <!--                        </div>-->
                <!--                    </div>-->
                                    
                <!--                    <div class="swiper-slide">-->
                <!--                        <div class="login-img">-->
                <!--                            <img src="{{ asset('admin/images/man-brand.jpg') }}" class="img-fluid" />-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <div class="col-12 col-md-6">
                    <div class="login-form">
                        <div class="login__right">
                            <div class="login__block">
                              <div class="logo__block">
                                <img src="{{ asset('admin/images/sales_drive_logo.jpeg') }}">
                              </div>
                    
                              @if (Session::get('success'))<div class="alert alert-success">{{ Session::get('success') }}</div>@endif
                              @if (Session::get('failure'))<div class="alert alert-danger">{{ Session::get('failure') }}</div>@endif
                    
                              <form method="POST" action="{{ route('admin.login.check') }}">
                              @csrf
                                <div class="form-floating mb-3">
                                  <input type="email" class="form-control" name="email" value="{{ old('email') }}" id="floatingInput" placeholder="name@example.com">
                                  <label for="floatingInput">Email address</label>
                                </div>
                                @error('email') <p class="small text-danger">{{ $message }}</p> @enderror
                    
                                <div class="form-floating mb-3">
                                  <input type="password" class="form-control" name="password" id="floatingPassword" placeholder="Password">
                                  <label for="floatingPassword">Password</label>
                                </div>
                                @error('password') <p class="small text-danger">{{ $message }}</p> @enderror
                    
                                {{-- <div class="row mb-3">
                                  <div class="col-6">
                                    <div class="form-check">
                                      <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                      <label class="form-check-label" for="flexCheckDefault">
                                        Remember Me
                                      </label>
                                    </div>
                                  </div>
                                  <div class="col-6 text-end">
                                    <a href="{{ route('admin.forget.password.get') }}">Forgot Password?</a>
                                  </div>
                                </div> --}}
                    
                                <div class="d-grid">
                                  <button type="submit" class="btn btn-lg btn-primary darkBlue-btn">Login</button>
                                </div>
                              </form>
                    
                              {{-- <div class="row mt-3">
                                  <div class="col-12 text-center">
                                    <a href="{{ url('/') }}">Back to homepage</a>
                                  </div>
                                </div> --}}
                            </div>
      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="{{ asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    
    <script>
        var swiper = new Swiper(".swiper-login-slider", {
          slidesPerView: 1,
          spaceBetween: 10,
          // effect: 'fade',
        //   fadeEffect: {
        //     crossFade: true
        //   },
          grabCursor: true,
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
          loop: true,
          autoplay: {
            delay: 5000,
            disableOnInteraction: false,
          },
          speed: 700,
          navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
          },
        //   breakpoints: {
        //     // when window width is >= 320px
        //     100: {
        //       slidesPerView: 2,
        
        //     },
        //     320: {
        //       slidesPerView: 2,
        //     },
        //     // when window width is >= 480px
        //     480: {
        //       slidesPerView: 1,
        //     },
        //     768: {
        //       slidesPerView: 3,
        //     },
        //     1024: {
        //       slidesPerView: 4,
        //     }
        //   },
        });
    </script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>
