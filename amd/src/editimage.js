define(['local_imgfilemanager/cropper','jquery'], function(Cropper,$) {
  var cropper;
  var originalImageURL;

  return {

      init: function(srcimg) {



      function getBase64Image(img) {
        var canvas = document.createElement("canvas");
        canvas.width = img.width;
        canvas.height = img.height;
        var ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0);
        var dataURL = canvas.toDataURL("image/png");
        return dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
      }
      //var Cropper = window.Cropper;
      var options;
      var image = document.getElementById('src-img');
      var actions = document.getElementById('actions');

      // if (srcimg != null) {
      //   image.src = srcimg;
      // }

        options = {
          aspectRatio: 800 / 200,
          autoCrop: false,
          dragMode: 'none',
          zoomOnWheel: false,
          ready: function(e) {},
        };

      cropper = new Cropper(image, options);
      originalImageURL = image.src;


      // Methods
      actions.querySelector('.docs-buttons').onclick = function(event) {
        var e = event || window.event;
        var target = e.target || e.srcElement;
        var cropped;
        var result;
        var input;
        var data;

        if (!cropper) {
          return;
        }

        data = {
          method: target.getAttribute('data-method'),
          target: target.getAttribute('data-target'),
          option: target.getAttribute('data-option') || undefined,
          secondOption: target.getAttribute('data-second-option') || undefined
        };

        console.log(data.method + ', ' + data.target + ', ' + data.option + ', ' + data.secondOption);

        if (data.method) {
          debugger;

          if (data.method == 'restore') {
            try {
              image.src = originalImageURL;
              cropper.destroy();
              cropper = new Cropper(image, options);
            } catch (e) {
              console.log(e.message);
            }
          } else {
            result = cropper[data.method](data.option, data.secondOption);
          }

          switch (data.method) {
            case 'rotate':
              if (cropped) {
                cropper.crop();
              }

              break;

            case 'scaleX':
            case 'scaleY':
              target.setAttribute('data-option', -data.option);
              break;

            case 'getCroppedCanvas':
              if (result) {
                image.src = result.toDataURL();
                cropper.destroy();
                cropper = new Cropper(image, options);
              }
              break;

          }
          $('#btn64').on('click', function() {
            debugger;
            var dataUrl= cropper.getCroppedCanvas().toDataURL();
            $('#id_image').val(dataUrl);

          });
          if (typeof result === 'object' && result !== cropper && input) {
            try {
              input.value = JSON.stringify(result);
            } catch (e) {
              console.log(e.message);
            }
          }
        }
      };


    }
    }
});