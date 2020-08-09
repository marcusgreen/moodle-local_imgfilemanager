define(['local_imagefilepicker/cropper','jquery'], function(Cropper,$) {
  var cropper;
  var originalImageURL;

  return {

      init: function(srcimg) {
      //var Cropper = window.Cropper;
      var URL = window.URL || window.webkitURL;
      var options;
      var image = document.getElementById('src-img');
      var download;
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