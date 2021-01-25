<?php $__env->startSection('title', 'Update setting'); ?>
<?php $__env->startSection('active'); ?>
<li class="breadcrumb-item">Settings</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startSection('content'); ?>
<div class="content-body">

<section id="input-with-icons">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"><?php echo e(config('app.name')); ?> Settings</h4>
                    <?php echo $__env->make('includes.alerts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="<?php echo e(route('settings.update', $setting->id)); ?>" method="POST" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <div class="row">
                                <div class="col-12">
                                    <p>You can set Application variables here</p>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="conference_theme">Conference theme</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="conference_theme" value="<?php echo e(old('conference_theme') ?? $setting->conference_theme); ?>" id="conference_theme">
                                        
                                        <div class="form-control-position">
                                           &#8962; 
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="registration_fee">Registration Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="registration_fee" value="<?php echo e(old('registration_fee') ?? $setting->registration_fee); ?>" id="registration_fee">
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="official_email">Official Email</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="text" class="form-control" name="official_email" value="<?php echo e(old('official_email') ?? $setting->official_email); ?>" id="official_email">
                                        <div class="form-control-position">
                                            &#128231;
                                        </div>
                                       
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="alumni_fee">Alumni Fee</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="number" class="form-control" name="alumni_fee" value="<?php echo e(old('alumni_fee') ?? $setting->alumni_fee); ?>" id="alumni_fee">
                                        <div class="form-control-position">
                                            &#8358;
                                        </div>
                                    </fieldset>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <label for="start_date">Conference Start Date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="start_date" value="<?php echo e(old('start_date') ?? $setting->start_date); ?>" id="start_date">
                                       
                                       <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>
                                 <div class="col-sm-6 col-md-6">
                                    <label for="end_date">Conference End Date</label>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="date" class="form-control" name="end_date" value="<?php echo e(old('end_date') ?? $setting->end_date); ?>" id="end_date">
                                       <div class="form-control-position">
                                            &#128197;
                                        </div>
                                    </fieldset>
                                </div>
                                
                                
                                
                                <div class="col-sm-12 col-md-12">
                                    <label for="conference_overview">Conference Overview</label><small> You can use html tags here</small>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <textarea class="form-control" id="conference_overview" rows="3" name="conference_overview"><?php echo old('conference_overview') ??$setting->conference_overview; ?></textarea>
                                        <div class="form-control-position">
                                           &#9745;
                                        </div>
                                    </fieldset>
                                </div>
                                
                                
                            </div>
                            <button class="btn btn-primary" style="width:100%; margin-top:10px" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.quilljs.com/1.0.5/quill.min.js" type="text/javascript"></script>
    <script>

        var currentEditor; // selected / focused editor
        var currentFormats; // save the current formattings

        createEditor("#editor1");
        createEditor("#editor2");

        function createEditor(selector)
        {
            let quill = new Quill(selector, { });
            
            quill.on("editor-change", (eventName, ...args) =>
            {
                currentEditor = quill;
                updateButtons();
            });
        }

        // get current formattings to style the toolbar buttons
        function updateButtons()
        {
            if(currentEditor.getSelection())
            {
                currentFormats = currentEditor.getFormat();

                if(currentFormats.bold)
                {
                    bold.classList.add("active");
                }
                else
                {
                    bold.classList.remove("active");
                }
            }
        }

        // if selected text is bold => unbold it - if it isn't => bold it
        function onBoldClick()
        {
            if(!currentFormats || !currentEditor)
            {
                return;
            }

            if(currentFormats.bold)
            {
                currentEditor.format("bold", false);
            }
            else
            {
                currentEditor.format("bold", true);                
            }
        }
        
    </script>
	
</section>
    
    <!-- Basic Inputs end -->          
</div>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Laravel Projects\GSF\resources\views/admin/settings/edit.blade.php ENDPATH**/ ?>