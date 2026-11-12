@push ('styles')
<style>
   .navbar-toggler-icon {
      background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='orange' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
   }
   .navbar-nav .nav-link {
      border: none !important;
   }

   .navbar-nav .nav-item {
      border-bottom: none !important;
   }
</style>

      <div class="container">
         <div class="row">
            <div class="col">
               <nav class="navbar navbar-expand-md justify-content-between">                  
                  <a class="navbar-brand p-0 m-0" href="{{route('index')}}">
                     <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}" alt="CISCE Logo" title="" class="img-fluid goforfit-logo d-none d-md-none d-lg-block" width="100" height="60">
                     <img src="{{ asset('resources/images/cisce/cisce-logo-prt.png') }}" class="img-fluid goforfit-logo logo-mob d-md-block d-lg-none d-sm-block" alt="CISCE Logo" title="">
                  </a>

                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                     <span class="navbar-toggler-icon"></span>
                  </button>

                  <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                     <ul class="navbar-nav text-center">
                        
                        <li class="nav-item">
                           <a class="nav-link {{ (request()->is('contact_us')) ? 'active' : '' }}" 
                              href="{{ route('contact_us') }}" style="color:#e67400;">
                              Contact Us
                           </a>
                        </li>

                        <li class="nav-item">
                           <a class="nav-link {{ (request()->is('self.registration')) ? 'active' : '' }}" 
                              href="{{ route('faq') }}" style="color:#e67400;">
                              FAQ
                           </a>
                        </li>
                        


                     </ul>
                  </div>

               </nav>
            </div>
         </div>
      </div>
