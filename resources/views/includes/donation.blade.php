<div class="container">
    <div class="row align-items-center">
        <div class="col-md-12">

            <div class="contact-form">
                <div id="success"></div>
                <form action="" method="POST">
                    @csrf
                    <div class="control-group">
                        <label>DONATION</label>
                        <input type="text" class="form-control" id="membership_id" name="membership_id"
                            placeholder="Enter Your GSF membership ID" required="required"
                            data-validation-required-message="Please enter your GSF membership ID" />

                        @if($errors->has('any'))
                            <span class="help-block">
                                <i style="color:red">{{ $errors->first('any') }}</i>
                            </span>
                        @endif
                    </div>

                    <div class="control-group">
                        <label>Location of training</label>
                        <select name="location" id="location" class="form-control" required>
                            <option value="">-- Select Option --</option>
                            <option value="Online"
                                {{ old('location') == 'Online' ? 'selected' : '' }}>
                                Online</option>
                            <option value="Ikeja"
                                {{ old('location') == 'Ikeja' ? 'selected' : '' }}>
                                Ikeja</option>
                            <option value="Lekki"
                                {{ old('location') == 'Lekki' ? 'selected' : '' }}>
                                Lekki</option>
                            <option value="Abuja"
                                {{ old('location') == 'Abuja' ? 'selected' : '' }}>
                                Abuja</option>
                            <option value="PHC"
                                {{ old('location') == 'PHC' ? 'selected' : '' }}>
                                PHC</option>
                            <option value="Accra"
                                {{ old('location') == 'Accra' ? 'selected' : '' }}>
                                Accra</option>
                            <option value="Banjul"
                                {{ old('location') == 'Banjul' ? 'selected' : '' }}>
                                Banjul</option>
                            <option value="Freetown"
                                {{ old('location') == 'Freetown' ? 'selected' : '' }}>
                                Freetown</option>
                            <option value="Monrovia "
                                {{ old('location') == 'Monrovia ' ? 'selected' : '' }}>
                                Monrovia </option>
                        </select>
                        @if($errors->has('location'))
                            <span class="help-block">
                                <i style="color:red">{{ $errors->first('location') }}</i>
                            </span>
                        @endif
                    </div>
                    <div class="control-group">
                        <label>Diet</label>
                        <select name="diet" id="diet" class="form-control" required>
                            <option value="">-- Select Option --</option>
                            <option value="2019 1st diet [May-June]"
                                {{ old('diet') == '2019 1st diet [May-June]' ? 'selected' : '' }}>
                                2019 1st diet [May - June]</option>
                            <option value="2019 -2nd diet [Oct - Nov]"
                                {{ old('diet') == '2019 -2nd diet [Oct - Nov]' ? 'selected' : '' }}>
                                2019 -2nd diet [Oct - Nov]</option>
                            <option value="2020 - 1st Diet [June-July]"
                                {{ old('diet') == '2020 - 1st Diet [June-July]' ? 'selected' : '' }}>
                                2020 - 1st Diet [June - July]</option>
                            <option value="2020- 2nd diet [Oct-Nov]"
                                {{ old('diet') == '2020- 2nd diet [Oct-Nov]' ? 'selected' : '' }}>
                                2020 - 2nd diet [Oct - Nov]</option>
                        </select>
                        @if($errors->has('diet'))
                            <span class="help-block">
                                <i style="color:red">{{ $errors->first('diet') }}</i>
                            </span>
                        @endif
                    </div>
                    <br>
                    <div class="control-group">
                        <button class="btn" type="submit" style="width:100%">Submit Application</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>