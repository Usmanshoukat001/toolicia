@extends('layouts.app')
@section('title', 'Image Compressor - Compress Image Size Online')
@section('description', 'Shrink the size of your images & make them easy to store and share. No technical skill is required to compress images with our image compressor.')
@section('content')

@endsection
@section('script')
<script>

    $(document).on('click', '#best-optimize', function() {
        var imageName = $(this).find('.imageName').text();
        var load = $(this).find('#load').removeClass('d-none');
        var currentDomain = window.location.hostname;

        var imageURL = 'https://' + currentDomain + '/storage/' + imageName;

        var img = new Image();
        img.crossOrigin = "Anonymous";
        img.src = imageURL;

        img.onload = function() {
            setTimeout(function() {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');

                canvas.width = img.width;
                canvas.height = img.height;

                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                var compressionQuality = 0.25;
                var compressedDataURL = canvas.toDataURL('image/jpeg', compressionQuality);

                var blob = dataURLtoBlob(compressedDataURL);

                var downloadLink = document.createElement('a');
                downloadLink.href = URL.createObjectURL(blob);
                downloadLink.download = imageName + '_compressed.jpg';

                document.body.appendChild(downloadLink);
                downloadLink.click();

                document.body.removeChild(downloadLink);
                load.addClass('d-none');
            }, 200);
        };


    });

    function dataURLtoBlob(compressedDataURL) {
        var arr = compressedDataURL.split(',');
        var mimeMatch = arr[0].match(/:(.*?);/);
        if (!mimeMatch) {
            throw new Error('Invalid data URL format: Missing MIME type');
        }

        var mime = mimeMatch[1];
        var bstr = atob(arr[1]);
        var n = bstr.length;
        var u8arr = new Uint8Array(n);

        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }

        return new Blob([u8arr], {
            type: mime
        });
    }



    $(document).on('click', '#image-compress', function() {
        var element = $(this).closest('.dz-preview');
        var imageName = element.find('.imageName').text();
        var mainDomain = window.location.protocol + "//" + window.location.hostname;
        $("#custom-image-show").attr("src", "" + mainDomain + "/storage/" + imageName);
        $('#custom-compress').modal('show');
        $('#compression-percentage').val(0);
        $('#compression-value').text(0);
        $('#custom-image-name').val(imageName);
        $("#Estimated-Size").text("Estimated Size:")
    });
    var timeoutId;

  $(document).on('input', '#compression-percentage', function() {
    $("#load-compress").removeClass('d-none');
    var compressionValueDefault = $(this).val();
    $('#compression-value').text(compressionValueDefault);

    var imageName = $('#custom-image-name').val();

    var imageUrl = 'https://' + window.location.hostname + '/storage/' + imageName;
    var compressValue = 100 - compressionValueDefault;
    if (compressValue === 0) {
        compressValue = 1;
    }

    if (imageName) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
            processImage(imageUrl, Math.abs(compressValue));
        }, 300);
    }
});

function processImage(imageUrl, compressionValueDefault) {
    imageUrl += '?cache=' + new Date().getTime();
    var img = new Image();
    img.src = imageUrl;
    img.crossOrigin = "Anonymous";

    img.onload = function() {
        var originalSize = getImageSize(img);

        var compressedSize = simulateCompression(originalSize, compressionValueDefault);
        var formattedSize = formatSize(compressedSize);

        $("#Estimated-Size").text("Estimated Size: " + formattedSize);

        $("#load-compress").addClass('d-none');
        setTimeout(function() {
            compressImage(imageUrl, compressedSize, compressionValueDefault);
        }, 0);
    };
}

function getImageSize(img) {
    return img.naturalWidth * img.naturalHeight;
}

function simulateCompression(originalSize, compressionValueDefault) {
    return originalSize * (compressionValueDefault / 100);
}

function formatSize(sizeInBytes) {
    var sizeInKB = sizeInBytes / 1024;

    if (sizeInKB < 1024) {
        return sizeInKB.toFixed(2) + " KB";
    } else {
        var sizeInMB = sizeInKB / 1024;
        if (sizeInMB < 1024) {
            return sizeInMB.toFixed(2) + " MB";
        } else {
            var sizeInGB = sizeInMB / 1024;
            return sizeInGB.toFixed(2) + " GB";
        }
    }
}

function compressImage(imageUrl, compressedSize, compressionValueDefault) {
    var img = new Image();
    img.src = imageUrl;
    img.crossOrigin = "Anonymous";

    img.onload = function() {
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');

        canvas.width = img.width;
        canvas.height = img.height;

        ctx.drawImage(img, 0, 0);

        // Apply compression by drawing the image on the canvas with the specified quality
        var compressionQuality = 1 - (compressionValueDefault / 100);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

        // Get the compressed data URL with specific compression quality
        var compressedDataURL = canvas.toDataURL('image/jpeg', compressionQuality);

        // Calculate the compressed size
        var compressedSize = compressedDataURL.length * 0.75; // Approximate size in bytes

        // Format the compressed size for display
        var formattedSize = formatSize(compressedSize);

        // Display the formatted size
        $("#Estimated-Size").text("Estimated Size: " + formattedSize);

        // Create a link and trigger download
        var link = document.createElement('a');
        link.href = compressedDataURL;
        link.download = 'compressed_image.jpg'; // Set the desired filename
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    };
}

    $(document).ready(function() {
    $(document).on('click', '#custom-compress-image-download', function() {
        $("#load-compress").removeClass('d-none');
        var imageName = $('#custom-image-name').val();
        var imageUrl = 'https://' + window.location.hostname + '/storage/' + imageName;
        var element = $(this).closest('.modal-body');
        var percentvalue = element.find('#compression-percentage').val();
        

       
     
       
    });
});





</script>
@endsection