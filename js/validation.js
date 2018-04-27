$(document).ready(function() {
    $('#registration_Form').bootstrapValidator({
//        live: 'disabled',
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            first_name: {
                validators: {
                    notEmpty: {
                        message: 'The First name is required'
                    }
                }
            },
            last_name: {
                validators: {
                    notEmpty: {
                        message: 'The Last name is required'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: 'The email is required'
                    },
                    emailAddress: {
                    	message: 'please type valid email'
                    }
                }
            },
            mobile: {
            	validators: {
            		notEmpty: {
            			message: "The mobile number is required"
            		},
            		integer: {
            			message: "The mobile number must be numeric"
            		},
            		stringLength: {
                        max: 10,
                        min: 10,
                        message: 'The mobile number must be 10 digits'
                    }
            	}
            },
            password: {
                validators: {
                    notEmpty: {
                        message: 'The password field is required'
                    }
                }
            },
            c_password: {
                validators: {
                    notEmpty: {
                        message: 'The confirm password field is required'
                    },
                    identical: {
                        field: 'password',
                        message: 'The password and its confirm are not the same'
                    }
                }
            }
        }
    });
});