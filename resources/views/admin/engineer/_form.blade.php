@csrf
<div class="row mb-3">
    <label for="name" class="col-sm-2 col-form-label">Name <code>*</code></label>
    <div class="col-sm-10">
        <input class="form-control @error('name') is-invalid @enderror" required type="text" placeholder="Engineer Name"
            name="name" id="name" value="{{ old('name', $engineer->name ?? '') }}">
        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="email" class="col-sm-2 col-form-label">Email <code>*</code></label>
    <div class="col-sm-10">
        <input class="form-control @error('email') is-invalid @enderror" required type="email" placeholder="Email"
            name="email" id="email" value="{{ old('email', $engineer->email ?? '') }}">
        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
<div class="row mb-3">
    <label for="phone" class="col-sm-2 col-form-label">Mobile Number <code>*</code></label>
    <div class="col-sm-10">
        <input class="form-control @error('phone') is-invalid @enderror" required type="text" placeholder="10 digit mobile number"
            name="phone" id="phone" maxlength="10" pattern="[6-9][0-9]{9}" inputmode="numeric"
            value="{{ old('phone', $engineer->phone ?? '') }}">
        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
</div>
