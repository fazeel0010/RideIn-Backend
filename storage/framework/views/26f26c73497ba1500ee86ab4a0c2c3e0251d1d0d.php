<style>
  .goog-te-banner-frame.skiptranslate {
    display: none !important;
  } 
  body {
    top: 0px !important; 
  }
</style>
<header class="main-header hide">
    <!-- Logo -->
      <!-- mini logo for sidebar mini 50x50 pixels -->
    
      <!-- logo for regular state and mobile devices -->
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
        <span id="show_date_time" class="show_date_time" style="color:#303841; font-size:16px; line-height: 46px;"></span>
      <div class="navbar-custom-menu">
         <div id="google_translate_element" class="google-translate-element"></div>
         <?php
            if(LOGIN_USER_TYPE=='company'){
              $user = Auth::guard('company')->user();
              $company_user = true;
            }else{
              $user = Auth::guard('admin')->user();
              $company_user = false;
            }
          ?>
        <ul class="nav navbar-nav">
          <input type="hidden" id="current_time" value="<?php echo e(date('F d, Y H:i:s', time())); ?>">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                <?php if(!$company_user || $user->profile ==null): ?>
                  <img src="<?php echo e(url('admin_assets/dist/img/avatar04.png')); ?>" class="user-image" alt="User Image">
                <?php else: ?>
                  <img src="<?php echo e($user->profile); ?>" class="user-image" alt="User Image">
                <?php endif; ?>
              
              <span class="hidden-xs"><?php echo e((!$company_user)?$user->username:$user->name); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">

               <?php if(!$company_user || $user->profile ==null): ?>
                  <img src="<?php echo e(url('admin_assets/dist/img/avatar04.png')); ?>" class="img-circle" alt="User Image">
                <?php else: ?>
                  <img src="<?php echo e($user->profile); ?>" class="img-circle" alt="User Image">
                <?php endif; ?>

                <p>
                  <?php echo e((!$company_user)?$user->username:$user->name); ?>

                  <small>Member since <?php echo e(date('M. Y', strtotime($user->created_at))); ?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <?php if($company_user): ?>
                  <div class="pull-left">
                    <a href="<?php echo e(url('company/profile')); ?>" class="btn btn-default btn-flat">Profile</a>
                  </div>
                <?php endif; ?>

                 <div class="pull-right">
                  <a href="<?php echo e(url($company_user ? 'company/logout' : 'admin/logout')); ?>" class="btn btn-primary btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
         <!--  <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li> -->
        </ul>
      </div>
      <?php if($company_user): ?>
        <select id="js-currency-select" class="form-control" style="display: none;">
          <?php $__currentLoopData = $currency_select; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($code); ?>" <?php if(session('currency') == $code ): ?> selected="selected" <?php endif; ?> ><?php echo e($code); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      <?php endif; ?>
    </nav>
  </header>
  
  <div class="flash-container hide">
    <?php if(Session::has('message')): ?>
      <div class="alert text-center <?php echo e(Session::get('alert-class')); ?>" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        <?php echo e(Session::get('message')); ?>

    </div>
    <?php endif; ?>
  </div>

<style type="text/css">
  #js-currency-select{
    padding: 1px 7px;
    float:right;
    font-size: 13px;
    display: inline-block;
    color: #000;
    height: auto;
    margin:13px 6px 3px;
    border-color: rgb(169, 169, 169);
    width: auto;
  }
</style>
<?php /**PATH C:\laragon\www\rideinapril\resources\views/admin/common/header.blade.php ENDPATH**/ ?>