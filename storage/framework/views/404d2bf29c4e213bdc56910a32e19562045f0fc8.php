<?php $__env->startSection('sliders'); ?>
<?php echo $__env->make('includes.sliders', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('body'); ?>
    <!-- About Start -->
    <div class="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="section-header">
                        
                        <h2>Title of the conference</h2>
                    </div>
                    <div class="about-text">
                        <div >
                            <img class="center" src="<?php echo e(asset('frontend/img/yvonne.jpeg')); ?>" alt="Image" style=" border-radius: 50%; display: block; margin-left: auto; margin-right: auto; width: 20%;">
                        </div> 
                        <p>
                        Arising from the continuous growth in key service industries of financial services and telecommunications sectors of the West Africa economies, in 2014, key customer service professionals from the region with background in service delivery in banking and telecommunications started a
                        network of like minds educating and imparting customer service skills and training in this spheres.<br> <br> 
                            
                        This network combined education and best practices translating to grooming of service officers and operating systems for organizations. While Nigeria
                            and Ghana network of professionals pioneered this frontier, the network also attracted customer service practitioners from Cote D&rsquo;Ivore, The Gambia and Senegal.
                        </p>
                    
                        <a class="btn submit" href="https://theGSF.com/english/history" style="width:100%" target="_blank">Read more</a>
                    </div>
                </div>
            
            </div>
        </div>
    </div>
    <!-- About End -->

     <div class="contact" id="register">
    </div>
    <!-- Contact Start -->
    <div class="contact">
        <div class="container">
            <div class="section-header">
                <h2>Register</h2>
            </div>
            <div class="row align-items-center">
            
                <div class="col-md-12">
                    <p style="text-align:center">There are 3 categories of registration</p>
                    <?php echo $__env->make('includes.falerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-md-4 col-sm-6" id="individualregbutton">
                    <a class="btn submit" onclick="myFunction()" id="individualregbutton" data-toggle="tooltip" data-placement="top"
                        title="Individual registration" style="width:100%">Individual</a>
                </div>
                <div class="col-md-4 col-sm-6" id="fellowshipregbutton">
                    <a class="btn submit" onclick="myFunction2()" id="fellowshipregbutton" data-toggle="tooltip" data-placement="top"
                        title="Register on behalf of your fellowship members" style="width:100%">Fellowship</a>
                </div>
                <div class="col-md-4 col-sm-6" id="alumniregbutton">
                    <a class="btn submit" onclick="myFunction3()" id="alumniregbutton" data-toggle="tooltip" data-placement="top"
                        title="Register as an alumni"  style="width:100%">Alumni</a>
                </div>               
               
            </div>
        </div>
    </div>
    <div class="about" id="individualreg" style="display:none;">
        <?php echo $__env->make('includes.individualform', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
    <div class="about" id="fellowshipreg" style="display:none;">
        <?php echo $__env->make('includes.fellowshipform', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
     <div class="about" id="alumnireg" style="display:none;">
        <?php echo $__env->make('includes.alumniform', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>    

    <div class="contact">
    </div>
    <div class="about">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <div class="section-header">
                        
                        <h2>Sponsor the conference</h2>
                    </div>
                    <div class="about-text">
                        <div >
                            <img class="center" src="<?php echo e(asset('frontend/img/network.jpeg')); ?>" alt="Image" style=" border-radius: 10%; display: block; margin-left: auto; margin-right: auto; width: 40%;"> <br>

                        </div> 
                        <p style="text-align:center">Are you led to sponsor the conference, no amount is too small nor big. Please click button below to donate
                        </p>
                       
                        <a class="btn submit" onclick="myFunction4()" id="donationbutton" data-toggle="tooltip" data-placement="top"
                        title="Register as an alumni"  style="width:100%">Make Donation</a>
                       
                        <div class="about" id="donation" style="display:none;">
                            <?php echo $__env->make('includes.donationform', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </div>
                       
                    </div>
                </div>
            
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('extra-scripts'); ?>
<script>
    var individualreg = document.getElementById("individualreg");
    var fellowshipreg = document.getElementById("fellowshipreg");
    var alunmireg = document.getElementById("alumnireg");
    var individualregbutton = document.getElementById("individualregbutton");
    var fellowshipregbutton = document.getElementById("fellowshipregbutton");
    var alumniregbutton = document.getElementById("alumniregbutton");
    var donation = document.getElementById("donation");
    
    function myFunction() {

    if (individualreg.style.display == "none") {
      individualreg.style.display = "block";
      fellowshipregbutton.style.display = "none";
      alumniregbutton.style.display = "none";
      donationbutton.style.display = "none";
    } 
    else {
      individualreg.style.display = "none";
      fellowshipregbutton.style.display = "block";
      alumniregbutton.style.display = "block";
      donationbutton.style.display = "block";
    }
  };

  function myFunction2() {

    if (fellowshipreg.style.display == "none") {
        fellowshipreg.style.display = "block";
        individualregbutton.style.display = "none";
        alumniregbutton.style.display = "none";
        donationbutton.style.display = "none";
    } 
    else {
        fellowshipregbutton.style.display = "block";
        fellowshipreg.style.display = "none";
        individualregbutton.style.display = "block";
        alumniregbutton.style.display = "block";
        donationbutton.style.display = "block";
    }
  }

   function myFunction3() {

    if (alumnireg.style.display == "none") {
        alumnireg.style.display = "block";
        individualregbutton.style.display = "none";
        fellowshipregbutton.style.display = "none";
        donationbutton.style.display = "none";
    } 
    else {
        fellowshipregbutton.style.display = "block";
        fellowshipreg.style.display = "none";
        alumnireg.style.display = "none";
        individualregbutton.style.display = "block";
        alumniregbutton.style.display = "block";
        donationbutton.style.display = "block";
    }
  }

  function myFunction4() {

    if (donation.style.display == "none") {
         donation.style.display = "block";
    } 
    else {
        donation.style.display = "block";
        donation.style.display = "none";
    }
  }
  

</script>
<script>
    $(document).ready(function() {
        $('.chapter').select2();
    });

    $(document).ready(function() {
        $('.chapterind').select2();
    }); 
    
                
</script>
                        
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\gsf\resources\views/welcome.blade.php ENDPATH**/ ?>