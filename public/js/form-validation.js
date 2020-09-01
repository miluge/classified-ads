// Wait for the DOM to be ready
$(function() {
    // Initialize form validation on the registration form.
    // It has the name attribute "add-form"
    $("form[name='form']").validate({
      // Specify validation rules
      rules: {
        // The key name on the left side is the name attribute
        // of an input field. Validation rules are defined
        // on the right side
        firstName: { 
          required: true
        }, 
        lastName: { 
          required: true
        },
        email: {
          required: true,
          // Specify that email should be validated
          // by the built-in "email" rule
          email: true
        },
        phone: {
          required: true,
          digits: true,
          minlength: 10
        },
        category_id: {
            required: true
        },
        title: {
            required: true,
            minlength: 5
        },
        description: {
            required: true,
            maxlength: 1500        
        }
            },
      // Specify validation error messages
      messages: {
        firstName: "Please enter your firstname",
        lastName: "Please enter your lastname",
        phone: {
          required: "Please provide a phone",
          minlength: "Your phone must be at least 10 characters long"
        },
        email: "Please enter a valid email address",
        category_id: "Please select a category",
        title: "Please enter a title",
        description: "Please enter a description"
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function(form) {
        form.submit();
      }
    });
  });