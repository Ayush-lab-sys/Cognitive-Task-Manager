  // document.addEventListener("DOMContentLoaded", function() {
  //   const inputs = document.querySelectorAll(".input-field");
  //   const toggle_btn = document.querySelectorAll(".toggle");
  //   const main = document.querySelector("main");
  //   const bullets = document.querySelectorAll(".bullets span");
  //   const images = document.querySelectorAll(".image");
  //   const textSlider = document.querySelector(".text-group");

  //   // Input field animation
  //   inputs.forEach((inp) => {
  //     inp.addEventListener("focus", () => inp.classList.add("active"));
  //     inp.addEventListener("blur", () => {
  //       if (inp.value === "") inp.classList.remove("active");
  //     });
  //   });

  //   // Toggle sign-up mode
  //   toggle_btn.forEach((btn) => {
  //     btn.addEventListener("click", () => {
  //       main.classList.toggle("sign-up-mode");
  //     });
  //   });

  //   // Move slider function with fixes
  //   function moveSlider() {
  //     let index = this.dataset.value;
  //     let currentImage = document.querySelector(`.image${index}`);

  //     if (!currentImage) {
  //       console.error(`Error: No element found with class .image${index}`);
  //       return; // Stop execution if the element does not exist
  //     }

  //     images.forEach((img) => img.classList.remove("show"));
  //     currentImage.classList.add("show");

  //     if (textSlider) {
  //       textSlider.style.transform = `translateY(${-(index - 1) * 2.2}rem)`;
  //     } else {
  //       console.error("Error: .text-group not found!");
  //     }

  //     bullets.forEach((bull) => bull.classList.remove("active"));
  //     this.classList.add("active");
  //   }

  //   // Attach click events to bullets
  //   bullets.forEach((bullet) => bullet.addEventListener("click", moveSlider));

  //   // Auto sliding carousel
  //   const carousel = () => {
  //     let currentSlide = 0;
    
  //     const moveSlider = () => {
  //       bullets.forEach((bull) => bull.classList.remove("active"));
  //       images.forEach((img) => img.classList.remove("show"));
    
  //       if (bullets.length > 0) {
  //         currentSlide = (currentSlide + 1) % bullets.length;
  //       }
        
    
  //       bullets[currentSlide].classList.add("active");
  //       images[currentSlide].classList.add("show");
    
  //       if (textSlider) {
  //         textSlider.style.transform = `translateY(${-currentSlide * 2.2}rem)`;
  //       }
  //     };
  //     console.log("Bullets found:", bullets.length);
  //     if (bullets.length === 0) {
  //       console.error("No bullets found! Check your HTML.");
  //     }
      
  //     setInterval(moveSlider, 2000);
    
  //     bullets.forEach((bullet) => {
  //       bullet.addEventListener("click", function () {
  //         bullets.forEach((bull) => bull.classList.remove("active"));
  //         images.forEach((img) => img.classList.remove("show"));
    
  //         const index = bullet.getAttribute("data-value") - 1;
  //         bullet.classList.add("active");
  //         images[index].classList.add("show");
    
  //         if (textSlider) {
  //           textSlider.style.transform = `translateY(${-index * 2.2}rem)`;
  //         }
    
  //         currentSlide = index;
  //       });
  //     });
  //   };
    
  //   carousel();
  // });


  document.addEventListener("DOMContentLoaded", function() {
    const inputs = document.querySelectorAll(".input-field");
    const toggle_btn = document.querySelectorAll(".toggle");
    const main = document.querySelector("main");
    const bullets = document.querySelectorAll(".bullets span");
    const images = document.querySelectorAll(".image");
    const textSlider = document.querySelector(".text-group");

    // Input field animation
    inputs.forEach((inp) => {
        inp.addEventListener("focus", () => inp.classList.add("active"));
        inp.addEventListener("blur", () => {
            if (inp.value === "") inp.classList.remove("active");
        });
    });

    // Toggle sign-up mode
    toggle_btn.forEach((btn) => {
        btn.addEventListener("click", () => {
            main.classList.toggle("sign-up-mode");
        });
    });

    // Initial state - ensure first image and bullet are active
    if (images.length > 0 && bullets.length > 0) {
        images[0].classList.add("show");
        bullets[0].classList.add("active");
    }

    // Auto sliding carousel
    let currentSlide = 0;
    const totalSlides = images.length;

    function updateSlider(index) {
        if (index < 0 || index >= totalSlides) return;

        // Remove show class from all images
        images.forEach(img => img.classList.remove("show"));
        
        // Add show class to current image
        images[index].classList.add("show");

        // Update bullets
        bullets.forEach(bull => bull.classList.remove("active"));
        bullets[index].classList.add("active");

        // Update text slider
        if (textSlider) {
            textSlider.style.transform = `translateY(${-index * 2.2}rem)`;
        }
    }

    // Handle bullet clicks
    bullets.forEach((bullet, index) => {
        bullet.addEventListener("click", () => {
            currentSlide = index;
            updateSlider(currentSlide);
        });
    });

    // Auto slide function
    function autoSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        updateSlider(currentSlide);
    }

    // Start auto sliding if we have images
    if (totalSlides > 0) {
        setInterval(autoSlide, 5000);
    }
});