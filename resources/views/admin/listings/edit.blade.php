<div class="card">
    <div class="card-content">
        <div class="card-body">
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <img style="max-width: 200px;" src="{{ $user->passport }}" alt="">
            </div>
            <div class="col-md-3 col-sm-12">
                <a class="btn btn-primary" href="{{route('user.single.approve', $user->id)}}" style="width:100%" type="submit">Approve</a>
            </div>
            <div class="col-md-3 col-sm-12">
                <a class="btn btn-primary" href="{{ route('user.single.approve', $user->id) }}" style="width:100%" type="submit">Reject</a>
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="name">Name</label>
                    <input disabled type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $user->name }}" placeholder="Enter name">
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="email">Email</label>
                    <input disabled type="email" id="email" name="email" class="form-control" value="{{ old('email') ?? $user->email }}" required>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="phone">Phone</label>
                    <input disabled type="phone" id="phone" name="phone" class="form-control" value="{{ old('phone') ?? $user->phone }}" required>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="sex">Gender</label>
                    <select disabled class="form-control" name="sex" id="sex" required>
                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
                    </select>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="open_to_work">Open to work? (this will add an open to work label on user profile)</label>
                    <select disabled class="form-control" name="open_to_work" id="open_to_work" required>
                        <option value="1" {{ $user->open_to_work == 1 ? 'selected' : ''}}>Yes</option>
                        <option value="0" {{ $user->open_to_work == 0 ? 'selected' : ''}}>No</option>
                    </select>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="chapter">Chapter</label>
                    <input disabled type="chapter" id="chapter" name="chapter" class="form-control" value="{{ $user->campus->name }}" disabled>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="sex">Campus Portfolio</label>
                    <select disabled class="form-control" name="portfolio" id="portfolio">
                        @foreach ( $portfolios as $key=>$portfolio)
                        <option value="{{ $key }}" {{ $user->role == $key ? 'selected' : ''}}>{{ $portfolio }}</option>
                        @endforeach
                    </select>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="program">Program</label>
                    <input disabled type="program" id="program" name="program" class="form-control" value="{{ $user->program }}" disabled>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="course">Course</label>
                    <input disabled type="course" id="course" name="course" class="form-control" value="{{ $user->course }}" disabled>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="matriculation_year">Matriculation Year</label>
                    <input disabled type="matriculation_year" id="matriculation_year" name="matriculation_year" class="form-control" value="{{ $user->matriculation_year }}" disabled>
                </fieldset>
            </div>
            <div class="col-md-6 col-sm-12">
                <fieldset class="form-group">
                    <label for="graduation_year">Graduation Year</label>
                    <input disabled type="graduation_year" id="graduation_year" name="graduation_year" class="form-control" value="{{ $user->graduation_year }}" disabled>
                </fieldset>
            </div>
        </div>
        
    </div>
</div>