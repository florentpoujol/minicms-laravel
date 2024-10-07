<?php
/**
 * @var \Illuminate\Support\ViewErrorBag $errors
 * @var string $key
 */
?>

@error($key)
<ul style="color: red;">
    @foreach($errors->get($key) as $message)
        <li>{{ $message }}</li>
    @endforeach
</ul><br>
@enderror
