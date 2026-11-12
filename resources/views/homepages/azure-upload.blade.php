<form action="{{ route('azure_upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="my_image">
    <button type="submit">Upload to Azure</button>
</form>