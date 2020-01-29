<?php
    global $part_args;
    $settings = $part_args['settings'];
    $title = $settings['title'];
    $intro = $settings['intro'];
    $content = $settings['content'];
?>

<div class="medical-form">
    <div class="steps-control">
        <span class="back">&nbsp;</span>
        <a href="#" data-step="1" class="active"><span class="mobile">1/4: </span> Introduction</a>
        <a href="#" data-step="2"><span class="mobile">2/4: </span> About You</a>
        <a href="#" data-step="3"><span class="mobile">3/4: </span> About Your Health</a>
        <a href="#" data-step="4"><span class="mobile">4/4: </span> Results</a>
    </div>

    <div class="steps">
        <form action="#" id="medical-form-form">
            <div class="step" id="step-1">
                <div class="title">
                    <h1 class="h1"><?php echo $title ?></h1>
                </div>
                <div class="inner">
                    <div class="intro">
                        <?php echo $intro ?>
                    </div>
                    <a href="#" class="btn continue">Get Started</a>
                    <div class="content">
                        <?php echo $content ?>
                    </div>
                </div>
            </div>
            <div class="step" id="step-2">
                <div class="title">
                    <h4 class="h4">About You</h4>
                </div>
                <div class="fields">
                    <div class="field input-text">
                        <input type="text" name="first-name" placeholder="First Name*" required>
                    </div>
                    <div class="field input-text">
                        <input type="text" name="last-name" placeholder="Last Name*" required>
                    </div>
                    <div class="field input-text">
                        <input type="email" name="email" placeholder="Email*" required>
                    </div>
                    <div class="field input-text">
                        <input type="date-of-birth" name="date-of-birth" class="dob" placeholder="Date of Birth (dd/mm/yy)*" required>
                    </div>
                    <div class="dt gender">
                        <div class="dtc">
                            <div class="radio-label dgrey bold font2">Your gender</div>
                        </div>
                        <div class="dtc">
                            <div class="field radio">
                                <label>
                                    <input type="radio" name="gender" value="Male" required>
                                    <span class="box">Male</span>
                                </label>
                                <label>
                                    <input type="radio" name="gender" value="Female" required>
                                    <span class="box last">Female</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="gender-note">
                        <p>Sorry, this medical form and product purchases are limited to men.</p>
                    </div>

                    <div class="submit">
                        <a href="#" class="btn continue">Next</a>
                    </div>
                </div>
            </div>
            <div class="step" id="step-3">
                <div class="title">
                    <h4 class="h4">About Your Health</h4>
                </div>
                <div class="fields">
                    <div class="dt">
                        <div class="dtc">
                            <div class="radio-label">Have you previously used Sildenafil or Cialis to treat symptoms of ED?</div>
                        </div>
                        <div class="dtc">
                            <div class="field radio">
                                <label>
                                    <input type="radio" name="symptoms-of-ed" value="Yes" required>
                                    <span class="box">Yes</span>
                                </label>
                                <label>
                                    <input type="radio" name="symptoms-of-ed" value="No" required>
                                    <span class="box last">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="dt">
                        <div class="dtc">
                            <div class="radio-label">Have you been advised not to use Sildenafil or Cialis by a medical practitioner?</div>
                        </div>
                        <div class="dtc">
                            <div class="field radio">
                                <label>
                                    <input type="radio" name="advised-not-to-use" value="Yes" required>
                                    <span class="box">Yes</span>
                                </label>
                                <label>
                                    <input type="radio" name="advised-not-to-use" value="No" required>
                                    <span class="box last">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="dt">
                        <div class="dtc">
                            <div class="radio-label">Do you get Angina, or are you taking any medication for Angina?</div>
                        </div>
                        <div class="dtc">
                            <div class="field radio">
                                <label>
                                    <input type="radio" name="do-you-get-angina" value="Yes" required>
                                    <span class="box">Yes</span>
                                </label>
                                <label>
                                    <input type="radio" name="do-you-get-angina" value="No" required>
                                    <span class="box last">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="dt">
                        <div class="dtc">
                            <div class="radio-label">Are you currently taking any Nitrate medications (GTN patch, Mononitrates, etc?)</div>
                        </div>
                        <div class="dtc">
                            <div class="field radio">
                                <label>
                                    <input type="radio" name="taking-any-nitrate-medications" value="Yes" required>
                                    <span class="box">Yes</span>
                                </label>
                                <label>
                                    <input type="radio" name="taking-any-nitrate-medications" value="No" required>
                                    <span class="box last">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="dt">
                        <div class="dtc">
                            <div class="radio-label">Have you had a heart attack in the last 6 months?</div>
                        </div>
                        <div class="dtc">
                            <div class="field radio">
                                <label>
                                    <input type="radio" name="had-a-heart-attack" value="Yes" required>
                                    <span class="box">Yes</span>
                                </label>
                                <label>
                                    <input type="radio" name="had-a-heart-attack" value="No" required>
                                    <span class="box last">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="dt">
                        <div class="dtc">
                            <div class="radio-label">Have you ever had a stroke or TIA?</div>
                        </div>
                        <div class="dtc">
                            <div class="field radio">
                                <label>
                                    <input type="radio" name="had-a-stroke-or-tia" value="Yes" required>
                                    <span class="box">Yes</span>
                                </label>
                                <label>
                                    <input type="radio" name="had-a-stroke-or-tia" value="No" required>
                                    <span class="box last">No</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="submit">
                        <a href="#" class="btn continue">Next</a>
                    </div>
                </div>
            </div>

            <div class="step" id="step-4">
                <div class="title">
                    <div class="approved">
                        <h2 class="h1">Awesome! <br>You qualify for an account<span class="pink">.</span></h2>
                    </div>
                    <div class="not-approved">
                        <h2 class="h1">We need to double check your details<span class="pink">.</span></h2>
                    </div>
                </div>
                <div class="inner">
                    <div class="approved">
                        <h4 class="h4">Finish your account</h4>
                        <p>You're nearly there! Create a password to browse and purchase TGA approved E.D medication.</p>
                    </div>
                    <div class="not-approved">
                        <!-- <h4 class="h4">Finish your account</h4> -->
                        <p>We need to review your medical form because you answered yes to one of our questions.</p>
                        <p>For now, you can view our products, but you won't be able to make a purchase until our doctors have confirmed that our products are safe for you to use.</p>
                        <p>In the meantime, please fill in your details below to create your account.</p>
                    </div>

                    <p class="font2 dgrey bold">
                        Name: <span class="name-text">...</span> <br>
                        Email: <span class="email-text">...</span>
                    </p>

                    <div class="fields">
                        <div class="field input-text">
                            <input type="password" name="password" id="password" placeholder="Password*">
                        </div>
                        <div class="field input-text">
                            <input type="password" name="password2" id="password2" placeholder="Password*">
                        </div>
                        <p class="small">* password must contain at least 6 characters, an uppercase character and a number.</p>

                        <a href="#" class="btn continue">Finish</a>
                    </div>
                </div>
                <div class="success">
                    <div class="approved">
                        <h4 class="h4">Success</h4>
                        <p>Your account has been created successfully you can now purchase our products.</p>
                        <p>Click <a href="<?php echo home_url('shop') ?>" class="pink">here</a> to start.</p>
                    </div>
                    <div class="not-approved">
                        <h4 class="h4">Almost there</h4>
                        <p>Your account has been created successfully but we need to check your information before purchasing.</p>
                        <p>Click <a href="<?php echo home_url('contact-us') ?>" class="pink">here</a> if you need to contact us.</p>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
