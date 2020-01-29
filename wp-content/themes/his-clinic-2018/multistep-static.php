<?php
/*
Template Name: Multistep Static
*/
get_header(); 
?>

<div id="mf-app">
    <div class="container">
        <form action="#">


        <!--================================
                  Exception Templates Starts
       =================================-->

       <div class="mf-step" mf-step="9.2a" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>92aPlease provide details of your medication, herbs or supplements:</h2>
                </div>

                <div class="animate-inputs animate-textarea">
                        <div class="animate-input">
                            <textarea class="validate-for-next" type="text" required name="rfullname" id="rfullname" placeholder="Please provide details"></textarea>
                            <label for="rfullname">Medication Details</label>
                        </div>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled mf-stop disabled" goto-step="9.3">Next</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop hide-products" goto-step="9.2">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

       <div class="mf-step"  mf-step="7.1" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>7.1Do you have, or have you ever had, Heart Disease?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step7.1" value="yes7.1" id="yes7.1">
                        <label for="yes7.1" class="mf-stop" goto-step="7.3">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step7.1" value="no7.1" id="no7.1">
                        <label for="no7.1" class="mf-stop" goto-step="8">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="7">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

       <!-- final form 1-->
       <div class="mf-step"  mf-step="7.2" style="display:none;">
            <div class="mf-step__item mf-step__rform">
                    <div class="text-center">
                        <h2>7.2Your medical assessment needs further review</h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <p>We’re currently reviewing your medical form due to some of your answers. Your health is our number one priority, so until our doctors have given your the all clear you will be able to browse the products, but will be unable to purchase.</p>
                            <p>Please create an account to view products.</p>
                        </div>
                        <div class="col-lg-6 col-lg-offset-1">
                            <div class="animate-inputs">
                                <div class="animate-input">
                                    <input type="text" name="rfullname" id="rfullname">
                                    <label for="rfullname">Full Name</label>
                                </div>
                                <div class="animate-input">
                                    <input type="text" name="remail" id="remail">
                                    <label for="remail">Email</label>
                                </div>
                                <div class="animate-input">
                                    <input type="password" name="rpassword" id="rpassword">
                                    <label for="rpassword">Password</label>
                                    <img class="svg eye" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/eye.svg" />
                                </div>
                            </div>
                            <a href="#" class="btn filled">Browse Products</a>
                        </div>
                    </div>
            </div>
       </div>

       <!-- heart disease disqualify -->
       <div class="mf-step" mf-step="7.3" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>2.1We’re sorry, you don’t qualify.</h2>
                </div>

                <div class="mf-content mf-content__sorry">
                   <p>You've mentioned that you have experienced Heart Disease. This means that you don't qualify to view or purchase our prescription products.  </p>
                   <p> <a href="#">Made a mistake?</a> If you think there's been a mistake, please contact us. </p>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled">Contact Us</a>
                </div>
            </div>
       </div>


       <!-- final form 2-->
       <div class="mf-step"  mf-step="14.1" style="display:none;">
            <div class="mf-step__item mf-step__rform">
                    <div class="text-center">
                        <h2>9.2 popup Our doctors have prescribed the right treatment for you<span>.</span></h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <p>Based on your medical history and individual needs, our doctors have provided a personalised treatment</p> <br/>
                            <p>Please complete your account to view your prescription.</p>
                        </div>
                        <div class="col-lg-6 col-lg-offset-1">
                            <div class="animate-inputs">
                                <div class="animate-input">
                                    <input type="text" name="rfullname" id="rfullname">
                                    <label for="rfullname">Full Name</label>
                                </div>
                                <div class="animate-input">
                                    <input type="text" name="remail" id="remail">
                                    <label for="remail">Email</label>
                                </div>
                                <div class="animate-input">
                                    <input type="password" name="rpassword" id="rpassword">
                                    <label for="rpassword">Password</label>
                                    <img class="svg eye" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/eye.svg" />
                                </div>
                            </div>
                            <a href="#" class="btn filled">Complete account to order</a>
                        </div>
                    </div>
            </div>
       </div>


       <!-- hypertension/ lightheadedness -->
       <div class="mf-step" mf-step="10a.1" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>10a.1 Are you taking medication for Lightheadedness?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step11" value="yes11" id="yes11">
                        <label for="yes11" class="mf-stop" goto-step="11.1">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step11" value="no11" id="no11">
                        <label for="no11" class="mf-stop no" goto-step="10a.2">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="10a">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:82%;"></div>
                </div>
            </div>
       </div>

       <div class="mf-step" mf-step="10a.2" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>10a.2 Are you taking medication for Hypertension?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step11" value="yes11" id="yes11">
                        <label for="yes11" class="mf-stop" goto-step="11.1">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step11" value="no11" id="no11">
                        <label for="no11" class="mf-stop no" goto-step="11">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="10a">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:82%;"></div>
                </div>
            </div>
       </div>


        <!--================================
                  Common Templates Starts
       =================================-->
       <div class="mf-step" mf-step="2.1" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>2.1We’re sorry, you don’t qualify.</h2>
                </div>

                <div class="mf-content mf-content__sorry">
                    <p>Our prescription products are designed for male’s only, and you’ve mentioned that you’re female.</p>
                    <p>If you have a male partner and they’re struggling with ED, why not open up the conversation and invite them to complete our online medical form.
                    </p>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled">About His Clinic</a>
                </div>
            </div>
       </div>

        <!-- less than 18 -->
       <div class="mf-step" mf-step="3.1" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>3.1We’re sorry, you don’t qualify.</h2>
                </div>

                <div class="mf-content mf-content__sorry">
                     <p>You’ve mentioned that you’re under 18 years old. This means that you don’t qualify to view or purchase our prescription products.</p>
                    <p>There may be alternative solutions for you. Check out our treatments page to view other solutions.</p>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled">Treatments</a>
                </div>
            </div>
       </div>

        <!-- greater than 75 -->
       <div class="mf-step" mf-step="3.2" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>3.1We’re sorry, you don’t qualify.</h2>
                </div>

                <div class="mf-content mf-content__sorry">
                     <p>You’ve mentioned that you’re over 75 years old. This means that you don’t qualify to view or purchase our prescription products.</p>
                    <p>There may be alternative solutions for you. Check out our treatments page to view other solutions.</p>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled">Treatments</a>
                </div>
            </div>
       </div>

        <!-- disqalify step -->
       <div class="mf-step" mf-step="11.1" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>11.1We’re sorry, you don’t qualify.</h2>
                </div>

                <div class="mf-content mf-content__sorry">
                     <p>You’ve indicated that you have health conditions that make you ineligible to purchase our prescription products.</p> <br/>
                    <p>There may be alternative solutions for you. Check out our treatments page to view other solutions.</p>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled">Treatments</a>
                </div>
            </div>
       </div>

       <div class="mf-step" mf-step="11.2" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>11.2We’re sorry, you don’t qualify.</h2>
                </div>

                <div class="mf-content mf-content__sorry">
                    <p>Oh no! You haven’t had your blood pressure checked within the last 12 months.</p>
                    <p>You can have your blood pressure checked for free by your local doctor. After that, we'll be able to help.</p>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled">Treatments</a>
                </div>
            </div>
       </div>

        <!--================================
                  Common Templates Ends
       =================================-->


       <!--================================
                  First Info Step
       =================================-->
       <div class="mf-step" mf-step="1" style="display:non">
            <div class="mf-step__item">
                    <div class="text-center">
                        <h2>1Hi! Welcome to your online pharmacy<span>.</span></h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <p>Most men will experience erection problems from time to time. But It’s not something to worry about! The good news: we can help. </p>
                            <p>Before browsing Erectile Dysfunction treatments, we need you to answer a few quick questions. This will help us know what treatments are right for you.</p>
                        </div>
                        <div class="col-lg-6 col-lg-offset-1">
                            <ul class="home-lists">
                            <li><img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Australia_509021_1f1f1f.svg" />His Clinic is Australian owned and operated</li>
                            <li><img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Doctor_1396017_1f1f1f.svg" />Your information will be reviewed by Australian registered doctors via an online assessment</li>
                            <li><img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Shipping_191965_1f1f1f.svg" />Affordable pricing and free shipping</li>
                            <li><img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Pharmacy_563617_1f1f1f.svg" />All products discreetly shipped from <br/> Australian registered pharmacies</li>

                            </ul>

                            <a href="#" class="btn filled mf-next">Get Started</a>
                        </div>
                    </div>
            </div>
       </div>

      <!--================================
                Second Info Step
       ================================-->
       <div class="mf-step" mf-step="2" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>2What is your gender</h2>
                </div>

                <div class="img-radio">
                    <div class="img-radio-item">
                        <input type="radio" name="gender" value="male" id="male">
                        <label for="male" class="mf-next"> <img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_boy_2374482_1f1f1f.svg" /><span>Male</span></label>
                    </div>
                    <div class="img-radio-item">
                        <input type="radio" name="gender" value="female" id="female">
                        <label for="female" class="mf-stop" goto-step="2.1"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_girl_2374468_1f1f1f.svg" /><span>Female</span></label>
                    </div>
                </div>
            </div>

            <div class="mf-progress">
                    <a href="#" class="mf-prev"><img src="<?php echo get_stylesheet_directory_uri()?>/assets/img/caret-left.svg" />Back</a>
                    <div class="mf-progress__bar">
                        <div class="mf-progress__fill" style="width:10%;"></div>
                    </div>
            </div>
       </div>

      <!--================================
                Third Info Step
       ================================-->
       <div class="mf-step validate" mf-step="3" style="display:none;">
            <div class="mf-step__item">
                <div class="text-center">
                    <h2>3What is your date of birth?</h2>
                </div>

                <div class="animate-inputs text-center">
                    <div class="animate-input">
                        <!-- <input type="date" name="date" id="dob"> -->
                        <input type="tel" required name="date" id="dob" placeholder="dd/mm/yyyy" maxlength="10">
                        <label for="dob">Date of Birth (dd/mm/yy)</label>
                    </div>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled  year-validate disabled">Validate Age</a>
                </div>
            </div>

            <div class="mf-progress">
                    <a href="#" class="mf-prev">< Back</a>
                    <div class="mf-progress__bar">
                        <div class="mf-progress__fill" style="width:15%;"></div>
                    </div>
            </div>

       </div>

      <!--================================
                Fourth Info Step
       ================================-->
       <div class="mf-step" mf-step="4" style="display:none;">
            <div class="mf-step__item mf-step__hw">
                <div class="text-center">
                    <h2>4What is your height and weight?</h2>
                </div>

                <div class="animate-inputs">
                    <div class="mf-height">
                        <div class="animate-input">
                            <input type="number" name="height" id="height">
                            <label for="height">Height (Centimeters)</label>
                        </div>

                        <div class="chk-btn-wrap">
                            <input type="checkbox" name="heightchk" value="heightchk" id="heightchk">
                            <label for="heightchk">I don’t know</label>
                        </div>
                    </div>

                    <div class="mf-weight">
                        <div class="animate-input">
                            <input type="number" name="weightchk" id="weightchk">
                            <label for="weightchk">Weight (Kilograms)</label>
                        </div>
                        <div class="chk-btn-wrap">
                            <input type="checkbox" name="weight" value="weight" id="weight">
                            <label for="weight">I don’t know</label>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled mf-next disabled">Continue</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:25%;"></div>
                </div>
            </div>
       </div>

      <!--================================
                Fifth Info Step
       ================================-->
       <div class="mf-step" mf-step="5" style="display:none;">
            <div class="mf-step__item mf-step__diet">
                <div class="text-center">
                    <h2>5What is your main diet?</h2>
                </div>

                <div class="img-radio">
                    <div class="img-radio-item">
                            <input type="radio" name="diet" value="meat" id="meat">
                            <label for="meat" class="mf-next"> <img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Meat_1171611_1f1f1f.svg" /><span>Carnivore</span></label>
                    </div>

                    <div class="img-radio-item">
                            <input type="radio" name="diet" value="fish" id="fish">
                            <label for="fish" class="mf-next"> <img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Fish_1879852_1f1f1f.svg" /><span>Pescatarian</span></label>
                    </div>

                    <div class="img-radio-item">
                            <input type="radio" name="diet" value="egg" id="egg">
                            <label for="egg" class="mf-next"> <img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Egg_2373029_1f1f1f.svg" /><span>Vegan</span></label>
                    </div>

                    <div class="img-radio-item">
                            <input type="radio" name="diet" value="veg" id="veg">
                            <label for="veg" class="mf-next"> <img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_vegan_924440_1f1f1f.svg" /><span>Vegetarian</span></label>
                    </div>

                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:30%;"></div>
                </div>
            </div>
       </div>
      <!--================================
                Fiftha Info Step
       ================================-->
       <div class="mf-step" mf-step="5a" style="display:none;">
            <div class="mf-step__item mf-step__maintain">
                <div class="text-center">
                    <h2>5aIf prescribed, how often do you anticipate using this treatment for sexual activity?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="uses" value="uses1" id="uses1">
                        <label for="uses1" class="mf-next">4 uses per month</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="uses" value="uses2" id="uses2">
                        <label for="uses2" class="mf-next">6 uses per month</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="uses" value="uses3" id="uses3">
                        <label for="uses3" class="mf-next">8 uses per month</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="uses" value="uses4" id="uses4">
                        <label for="uses4" class="mf-next">10 uses per month</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>


      <!--================================
                Sixth Info Step
       ================================-->
       <div class="mf-step" mf-step="6" style="display:none;">
            <div class="mf-step__item mf-step__maintain">
                <div class="text-center">
                    <h2>6Do you ever have a problem getting or maintaining an erection?</h2>
                </div>

                <div class="text-center" style=" max-width: 768px; ">
                    <div class="radio-btn-wrap" style="justify-content: flex-start;">
                        <div class="radio-btn">
                            <input type="radio" name="maintain" value="maintain1" id="maintain1">
                            <label for="maintain1" class="mf-next">Yes, every time</label>
                        </div>
                        <div class="radio-btn">
                            <input type="radio" name="maintain" value="maintain2" id="maintain2">
                            <label for="maintain2" class="mf-next">Yes, more than half the time</label>
                        </div>
                        <div class="radio-btn">
                            <input type="radio" name="maintain" value="maintain3" id="maintain3">
                            <label for="maintain3" class="mf-next">Yes, on occasion</label>
                        </div>
                        <!-- <div class="radio-btn">
                            <input type="radio" name="maintain" value="maintain4" id="maintain4">
                            <label for="maintain4" class="mf-next">I never have a problem</label>
                        </div> -->
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

      <!--================================
                Seventh Info Step
       ================================-->
       <div class="mf-step" mf-step="7" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>
                        <div class="tooltip-heading">
                            <img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/noun_Information_558363_000000.svg" />
                            <p>If you haven’t previously been approved by a doctor to use these medications, you will need to have a short phone call with one of our male doctors.</p>
                        </div>
                        7Have you ever been prescribed or approved by a doctor to take <span data-content="1Generic Viagra
                        (lasts 4 hours)">Sildenafil</span> or <span data-content="2Generic Viagra
                        (lasts 4 hours)">Cialis</span>?
                    </h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step7" value="yes7" id="yes7">
                        <label for="yes7" class="mf-next">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step7" value="no7" id="no7">
                        <label for="no7" class="mf-stop" goto-step="7.1">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

      <!--================================
                Eigth Info Step
       ================================-->
       <div class="mf-step"  mf-step="8" style="display:none;">
            <div class="mf-step__item mf-step__chkbox mf-step__chkbox__validate">
                <div class="text-center">
                    <h2>8Have you previously used any of the following products?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input data-product="1" type="checkbox" name="step10.1" value="step7.01" id="step7.01">
                        <label for="step7.01"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Sildenafil (generic Viagra, lasts 4 hours)</label>
                    </div>
                    <div class="radio-btn">
                        <input data-product="2" type="checkbox" name="step7.02" value="step7.02" id="step7.02">
                        <label for="step7.02"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Cialis (lasts 36 hours)</label>
                    </div>
                    <div class="radio-btn">
                        <input data-product="3" type="checkbox" name="step7.03" value="step7.03" id="step7.03">
                        <label for="step7.03"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Daily Cialis (taken daily so you’re always ready)</label>
                    </div>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled mf-stop" goto-step="9">None</a>

                    <!-- goto both -->
                    <a href="#" class="btn filled mf-products" style="display:none;">Continue</a>

                    <!-- goto Cialis only -->
                    <!-- <a href="#" class="btn filled mf-stop" goto-step="8.1" style="display:none;">Continue</a>  -->

                    <!-- goto Sildenafil only -->
                    <!-- <a href="#" class="btn filled mf-stop" goto-step="8.1" style="display:none;">Continue</a>         -->
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>


        <!--================================
               Show Producsts if checked
       ================================-->
       <div class="products-wrapper">
        <div class="mf-product"  data-product="1" style="display:none;">
                <div class="mf-step__item mf-step__yesno">
                    <div class="text-center">
                        <h2>8.1Was Sildenafil effective?</h2>
                    </div>

                    <div class="radio-btn-wrap">
                        <div class="radio-btn">
                            <input type="radio" name="step8.1" value="yes8.1" id="yes8.1">
                            <label for="yes8.1" class="next-product">Yes</label>
                        </div>
                        <div class="radio-btn">
                            <input type="radio" name="step8.1" value="no8.1" id="no8.1">
                            <label for="no8.1" class="next-product">No</label>
                        </div>
                    </div>

                </div>
                <div class="mf-progress">
                    <a href="#" class="mf-stop hide-products" goto-step="8">< Back</a>
                    <div class="mf-progress__bar">
                        <div class="mf-progress__fill" style="width:35%;"></div>
                    </div>
                </div>
        </div>

        <div class="mf-product"  data-product="2" style="display:none;">
                <div class="mf-step__item mf-step__yesno">
                    <div class="text-center">
                        <h2>8.2Was Cialis effective?</h2>
                    </div>

                    <div class="radio-btn-wrap">
                        <div class="radio-btn">
                            <input type="radio" name="step8.2" value="yes8.2" id="yes8.2">
                            <label for="yes8.2" class="next-product">Yes</label>
                        </div>
                        <div class="radio-btn">
                            <input type="radio" name="8.2" value="no8.2" id="no8.2">
                            <label for="no8.2" class="next-product">No</label>
                        </div>
                    </div>

                </div>
                <div class="mf-progress">
                    <a href="#" class="mf-stop hide-products" goto-step="8">< Back</a>
                    <div class="mf-progress__bar">
                        <div class="mf-progress__fill" style="width:35%;"></div>
                    </div>
                </div>
        </div>

        <div class="mf-product"  data-product="3" style="display:none;">
                <div class="mf-step__item mf-step__yesno">
                    <div class="text-center">
                        <h2>8.3Was Daily Cialis effective?</h2>
                    </div>

                    <div class="radio-btn-wrap">
                        <div class="radio-btn">
                            <input type="radio" name="step8.2" value="yes8.2" id="yes8.2">
                            <label for="yes8.2" class="next-product">Yes</label>
                        </div>
                        <div class="radio-btn">
                            <input type="radio" name="8.2" value="no8.2" id="no8.2">
                            <label for="no8.2" class="next-product">No</label>
                        </div>
                    </div>

                </div>
                <div class="mf-progress">
                    <a href="#" class="mf-stop hide-products" goto-step="8">< Back</a>
                    <div class="mf-progress__bar">
                        <div class="mf-progress__fill" style="width:35%;"></div>
                    </div>
                </div>
        </div>
       </div>


        <!--================================
                Ninth Info Step
       ================================-->
       <div class="mf-step end-step-validate" mf-step="9" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9Do you have any past or ongoing medical conditions?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input  class="yes" type="radio" name="step9" value="yes9" id="yes9">
                        <label for="yes9" class="mf-next">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input  class="no" data-skip="10" type="radio" name="step9" value="no9" id="no9">
                        <label for="no9" class="mf-stop" goto-step="9.b">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop hide-products" goto-step="8">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

         <!--================================
                Ninth Sub Steps
       ================================-->
       <div class="mf-step" mf-step="9.a" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9aPlease provide as much detail as possible about your ongoing medical conditions:</h2>
                </div>

                <div class="animate-inputs animate-textarea">
                        <div class="animate-input">
                            <textarea class="validate-for-next" type="text" required name="rfullname" id="rfullname" placeholder="Please provide details"></textarea>
                            <label for="rfullname">Ongoing Medical Conditions</label>
                        </div>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled mf-next disabled">Next</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop hide-products" goto-step="9">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>


       <div class="mf-step end-step-validate" mf-step="9.b" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9bHave you had a history of hospital admission or surgeries?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input  class="yes" type="radio" name="step9b" value="yes9b" id="yes9b">
                        <label for="yes9b" class="mf-next">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input  class="no" data-skip="10" type="radio" name="step9b" value="no9b" id="no9b">
                        <label for="no9b" class="mf-stop" goto-step="9.d">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop hide-products" goto-step="9">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

       <div class="mf-step" mf-step="9.c" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9cPlease provide details about your history of hospital admission or surgery:</h2>
                </div>

                <div class="repeat-block">
                    <div class="animate-inputs animate-textarea">                  
                            <div class="animate-input">
                                <!-- <input type="date" name="date" id="dob"> -->
                                <input class="mask-date" required type="tel" name="date9.c" id="date9.c" placeholder="DD/MM/YYYY" maxlength="10">
                                <label for="date9.c">Date of hospital admission or surgery</label>
                            </div>
                            <div class="animate-input">
                                <textarea type="text" required name="textarea9.c" id="textarea9.c" placeholder="Please provide details"></textarea>
                                <label for="textarea9.c">Details about your hospital admission or surgery</label>
                            </div>
                    </div>              
                </div>

                <div id="add-more">
                    <span>ADD MORE</span>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled mf-next disabled">Next</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop hide-products" goto-step="9.b">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

       <div class="mf-step end-step-validate" mf-step="9.d" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9dDo you have any allergies?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input class="yes" type="radio" name="step9d" value="yes9d" id="yes9d">
                        <label for="yes9d" class="mf-next">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input class="no" data-skip="10" type="radio" name="step9d" value="no9d" id="no9d">
                        <label for="no9d" class="mf-stop" goto-step="9.1">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop hide-products" goto-step="9.b">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>

       <div class="mf-step" mf-step="9.e" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9aPlease provide as much detail as possible about your allergies:</h2>
                </div>

                <div class="animate-inputs animate-textarea">
                        <div class="animate-input">
                            <textarea class="validate-for-next" type="text" required name="rfullname" id="rfullname" placeholder="Please provide details"></textarea>
                            <label for="rfullname">Your allergies</label>
                        </div>
                </div>

                <div class="text-center">
                    <a href="#" class="btn filled mf-next disabled">Next</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop hide-products" goto-step="9.d">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>


      

        <!--================================
                Ninth.1 Info Step
       ================================-->
       <div class="mf-step"  mf-step="9.1" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9.1Are you currently taking any Nitrate medications (GTN patch, Mononitrates, etc)?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step9.1" value="yes9.1" id="yes9.1">
                        <label class="mf-stop" for="yes9.1" goto-step="11.1">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step9.1" value="no9.2" id="no9.2">
                        <label class="mf-next" for="no9.2">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="9.d">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:60%;"></div>
                </div>
            </div>
       </div>


        <!--================================
                Ninth.2 Info Step
       ================================-->
       <div class="mf-step"  mf-step="9.2" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9.2Are you taking any other medications, herbs or supplements?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input class="yes" type="radio" name="step9.2" value="yes9.22" id="yes9.22">
                        <label class="mf-stop" for="yes9.22" goto-step="9.2a">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input class="no" type="radio" name="step9.2" value="no9.2"3 id="no9.23">
                        <label class="mf-next" for="no9.23">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="9.1">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:60%;"></div>
                </div>
            </div>
       </div>

        <!--================================
                Ninth.3 Info Step
       ================================-->
       <div class="mf-step"  mf-step="9.3" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9.3You need to have your blood Pressure (BP) checked within the last 12 months to recieve treatment.</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step9.3" value="yes9.3" id="yes9.3">
                        <label class="mf-next" for="yes9.3">Yes - It’s been checked</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step9.3" value="no9.3" id="no9.3">
                        <label class="mf-stop" goto-step="11.2" for="no9.3">No - I haven’t had it checked</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="9.2">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:60%;"></div>
                </div>
            </div>
       </div>

        <!--================================
                Ninth.4 Info Step
       ================================-->
       <div class="mf-step"  mf-step="9.4" style="display:none;">
            <div class="mf-step__item mf-step__chkbox mf-step__stop">
                <div class="text-center">
                    <h2>9.4When your blood pressure was taken were you diagnosed with?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="checkbox" name="step9.4" value="step9.4" id="step9.4">
                        <label for="step9.4"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Hypertension (high blood pressure)</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step9.42" value="step9.42" id="step9.42">
                        <label for="step9.42"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Hypotension (low blood pressure)</label>
                    </div>
                </div>
                <input type="checkbox" name="blood_pressure_diagnosis" value="Normal" id="step9.43" checked="checked" class="mf-hidden-input">

                <div class="text-center">
                    <a href="#" class="btn filled mf-next">No, it was normal</a>
                    <a href="#" class="btn filled mf-stop" goto-step="11.1" style="display:none;">Continue</a>
                </div>

            </div>

            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="9.3">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:60%;"></div>
                </div>
            </div>
       </div>

        <!--================================
                Ninth.5 Info Step
       ================================-->
       <div class="mf-step"  mf-step="9.5" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>9.5Do you frequantly experience lightheadedness?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step9.5" value="yes9.5" id="yes9.5">
                        <label class="mf-stop" goto-step="11.1" for="yes9.5">Yes - It’s been checked</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step9.5" value="no9.5" id="no9.5">
                        <label class="mf-next" for="no9.5">No - I haven’t had it checked</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-stop" goto-step="9.4">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:60%;"></div>
                </div>
            </div>
       </div>

       <!--================================
                Tenth Info Step
       ================================-->
       <div class="mf-step"  mf-step="10" style="display:none;">
            <div class="mf-step__item mf-step__chkbox mf-step__stop">
                <div class="text-center">
                    <h2>10Do you have any of the following cardiovascular symptoms?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="checkbox" name="step9.4" value="step10.1" id="step10.1">
                        <label for="step10.1"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Abnormal heart beats (rapid, irregular, slow, murmurs)</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step10.2" value="step10.2" id="step10.2">
                        <label for="step10.2"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Chest pain (angina) or shortness of breath</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step10.3" value="step10.3" id="step10.3">
                        <label for="step10.3"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Episodes of unexplained fainting or dizziness</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step10.4" value="step10.4" id="step10.4">
                        <label for="step10.4"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Cramping pain in the calves or thighs with exercise</label>
                    </div>
                </div>

                <input type="checkbox" name="cardiovascular_symptoms" value="None Apply" id="step10" checked="checked" class="mf-hidden-input"/>

                <div class="text-center">
                    <a href="#" class="btn filled mf-next">None apply</a>
                    <a href="#" class="btn filled mf-stop" goto-step="11.1" style="display:none;">Continue</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:35%;"></div>
                </div>
            </div>
       </div>


       <!--================================
                Eleventh Info Step
       ================================-->
       <div class="mf-step" mf-step="11" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>11Have you had a heart attack in the last 6 months?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step11" value="yes11" id="yes11">
                        <label for="yes11" class="mf-stop" goto-step="11.1">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step11" value="no11" id="no11">
                        <label for="no11" class="mf-next">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:82%;"></div>
                </div>
            </div>
       </div>

       <!--================================
                Twelvth Info Step
       ================================-->
       <div class="mf-step" mf-step="12" style="display:none;">
            <div class="mf-step__item mf-step__yesno">
                <div class="text-center">
                    <h2>12Have you ever had a stroke or TIA?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn">
                        <input type="radio" name="step12" value="yes12" id="yes12">
                        <label for="yes12" class="mf-stop" goto-step="11.1">Yes</label>
                    </div>
                    <div class="radio-btn">
                        <input type="radio" name="step12" value="no12" id="no12">
                        <label for="no12" class="mf-next">No</label>
                    </div>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:86%;"></div>
                </div>
            </div>
       </div>

       <!--================================
                Thirteenth Info Step
       ================================-->
       <div class="mf-step"  mf-step="13" style="display:none;">
            <div class="mf-step__item mf-step__chkbox mf-step__stop">
                <div class="text-center">
                    <h2>13Do you have now, or have you ever had, any of the following conditions?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn radio-btn--full">
                        <input type="checkbox" name="step13.1" value="step13.1" id="step13.1">
                        <label for="step13.1"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Multiple Sclerosis or a similar disease, spinal injuries or paralysis or neurological diseases</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step13.2" value="step13.2" id="step13.2">
                        <label for="step13.2"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Surgery or radiation to the prostate or pelvis</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step13.3" value="step13.3" id="step13.3">
                        <label for="step13.3"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Stomach, intestinal or bowel ulcers</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step13.4" value="step13.4" id="step13.4">
                        <label for="step13.4"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Liver Disease</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step13.5" value="step13.5" id="step13.5">
                        <label for="step13.5"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Kidney transplant (or conditions affecting the kidney)</label>
                    </div>
                </div>

                <input type="checkbox" name="conditions_1" value="None Apply" id="step13" checked="checked" class="mf-hidden-input" />

                <div class="text-center">
                    <a href="#" class="btn filled mf-next">No, it was normal</a>
                    <a href="#" class="btn filled mf-stop" goto-step="11.1" style="display:none;">Continue</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:90%;"></div>
                </div>
            </div>
       </div>


       <!--================================
                Fourteenth Info Step
       ================================-->
       <div class="mf-step"  mf-step="14" style="display:none">
            <div class="mf-step__item mf-step__chkbox mf-step__stop">
                <div class="text-center">
                    <h2>14Do you have any of these conditions?</h2>
                </div>

                <div class="radio-btn-wrap">
                    <div class="radio-btn radio-btn--full">
                        <input type="checkbox" name="step14.1" value="step14.1" id="step14.1">
                        <label for="step14.1"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />A marked curve or bend in the penis that inteferes with sex, or Peyronie’s disease</label>
                    </div>
                    <div class="radio-btn radio-btn--full">
                        <input type="checkbox" name="step14.2" value="step14.2" id="step14.2">
                        <label for="step14.2"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Fibrous tissue in the penis (lumps and bumps under the skin that feels hard)</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step14.3" value="step14.3" id="step14.3">
                        <label for="step14.3"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />Pain with erections or ejaculation</label>
                    </div>
                    <div class="radio-btn">
                        <input type="checkbox" name="step14.3" value="step14.3" id="step14.3">
                        <label for="step14.3"><img class="svg" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/checkbox.svg" />A foreskin that is too tight</label>
                    </div>
                </div>

                <input type="checkbox" name="conditions_2" value="None Apply" id="step14" checked="checked" class="mf-hidden-input" />

                <div class="text-center">
                    <!-- <a href="#" class="btn filled mf-next">None apply</a> -->
                    <!-- <a href="#" class="btn filled mf-stop" goto-step="14.1" style="display:none;">None apply</a> -->
                    <!-- goto final form if no surgery-->
                    <a href="#" class="btn filled mf-stop" goto-step="15">None apply</a>
                </div>

            </div>
            <div class="mf-progress">
                <a href="#" class="mf-prev">< Back</a>
                <div class="mf-progress__bar">
                    <div class="mf-progress__fill" style="width:100%;"></div>
                </div>
            </div>
       </div>

       <!--================================
                Fifteenth Info Step
       ================================-->
       <!-- final form 3 -->
       <div class="mf-step"  mf-step="15" style="display:none">
            <div class="mf-step__item mf-step__rform">
                    <div class="text-center">
                        <h2>15You're almost there!</h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <p>Thanks for filling in your details!</p> <br/>
                            <p>Please create your account so that our doctors can get back to you after they've reviewed your details.</p>
                        </div>
                        <div class="col-lg-5 col-lg-offset-2">
                            <div class="animate-inputs">
                                <div class="animate-input">
                                    <input type="text" name="rfullname" id="rfullname" required>
                                    <label for="rfullname">Full Name</label>
                                </div>
                                <div class="animate-input">
                                    <input type="text" name="remail" id="remail" required>
                                    <label for="remail">Email</label>
                                </div>
                                <div class="animate-input">
                                    <input type="password" name="rpassword" id="rpassword" required>
                                    <label for="rpassword">Password</label>
                                    <img class="svg eye" src="<?php echo get_stylesheet_directory_uri()?>/assets/img/eye.svg" />
                                </div>
                            </div>
                            <a href="#" class="btn filled mf-next">Create Account</a>
                        </div>
                    </div>
            </div>
       </div>

       <!--================================
                Sixteenth Info Step
       ================================-->
       <!-- final form 4 -->
       <div class="mf-step"  mf-step="16" style="display:none">
            <div class="mf-step__item mf-step__rform">
                    <div class="text-center">
                        <h2>16Your medical assessment needs further review</h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <p>Based on your answers you have unspecified health conditions or a history of prior surgeries.</p> <br/>
                            <p>Please input the details about these circumstances so that we can ensure our products are safe for you to use. You'll be able to purchase our products after your medical information has been reviewed.</p>
                        </div>
                        <div class="col-lg-5 col-lg-offset-2">
                            <div class="animate-inputs">
                                <div class="animate-input">
                                    <textarea type="text" required name="rfullname" id="rfullname" placeholder="Joe Smith"></textarea>
                                    <label for="rfullname">Medical Details</label>
                                </div>
                            </div>
                            <a href="#" class="btn filled">Finish</a>
                        </div>
                    </div>
            </div>
       </div>

       <!-- loader -->
       <!-- <div class="loader" style="display:none;">
         <div class="lds-ring"><div></div><div></div><div></div><div></div></div> 
       </div> -->

     </form>
    </div>
</div>



<!-- Modal for other pages -->
<!-- <button id="myBtn">Open Modal</button>
<div id="myModal" class="modal" style="display: block;">

  <div class="modal-content">
    <span class="close">×</span>
    <div class="mf-step" mf-step="7.2" style="/* display:none; */">
            <div class="mf-step__item mf-step__rform">
                    <div class="text-center">
                        <h2>7.2Your medical assessment needs further review</h2>
                    </div>
                    <div class="row">
                        <div class="col-lg-5">
                            <p>We’re currently reviewing your medical form due to some of your answers. Your health is our number one priority, so until our doctors have given your the all clear you will be able to browse the products, but will be unable to purchase.</p>
                            <p>Please create an account to view products.</p>
                        </div>
                        <div class="col-lg-6 col-lg-offset-1">
                            <div class="animate-inputs">
                                <div class="animate-input">
                                    <input type="text" name="rfullname" id="rfullname" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAZdJREFUOBGVVD2LwkAQfTEHfhTXqIXggY0KNoKtEBBsDv0Zh0JI5w9Qf4bXWPkHBPHqWKq1CFaeWImIwS8w5Hai2UskxLiw2Zk3b97uziwR2u32p2EYHTY/4GMIgvDLaLVWq/VD9MAryZRw3+ibbBok4GvnG/32tecE7IFndiQSQSaTcdBeEkilUiiXy+4CoVCIB4LBIFixuG+PcfBuvNFarVZRKBTQ6/Ww3W4hyzLW6zW63a4j9phM/ktXcBMQms2mQQE65vl8Njl0hcvl4sbnGLviijl1fgIrmRjPkonDWplkS8esAQFeI51OQ5IkxGIxLJdLDAYD7Pd7U4SfwEugWCwiGo1iNBohm80il8txeuD+tjngZsxmM4TDYeTzeRyPRywWC04jgfozkd1uB13XEY/HzTafTicu8P9aOHQzrO6Q12g0sNls0O/3oSgKJpMJhsOhSfRVg8PhgEQigUqlAlEUQb41RMt4XEul0hfD3gmfz+e4Xq/mnE6nGI/H1AF67iuvNlJt6EeT1DQNqqo69qBkBtT/ANzCmpZx1xTXAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;" autocomplete="off">
                                    <label for="rfullname">Full Name</label>
                                </div>
                                <div class="animate-input">
                                    <input type="text" name="remail" id="remail">
                                    <label for="remail">Email</label>
                                </div>
                                <div class="animate-input">
                                    <input type="password" name="rpassword" id="rpassword" style="background-image: url(&quot;data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAASCAYAAABSO15qAAAAAXNSR0IArs4c6QAAAZdJREFUOBGVVD2LwkAQfTEHfhTXqIXggY0KNoKtEBBsDv0Zh0JI5w9Qf4bXWPkHBPHqWKq1CFaeWImIwS8w5Hai2UskxLiw2Zk3b97uziwR2u32p2EYHTY/4GMIgvDLaLVWq/VD9MAryZRw3+ibbBok4GvnG/32tecE7IFndiQSQSaTcdBeEkilUiiXy+4CoVCIB4LBIFixuG+PcfBuvNFarVZRKBTQ6/Ww3W4hyzLW6zW63a4j9phM/ktXcBMQms2mQQE65vl8Njl0hcvl4sbnGLviijl1fgIrmRjPkonDWplkS8esAQFeI51OQ5IkxGIxLJdLDAYD7Pd7U4SfwEugWCwiGo1iNBohm80il8txeuD+tjngZsxmM4TDYeTzeRyPRywWC04jgfozkd1uB13XEY/HzTafTicu8P9aOHQzrO6Q12g0sNls0O/3oSgKJpMJhsOhSfRVg8PhgEQigUqlAlEUQb41RMt4XEul0hfD3gmfz+e4Xq/mnE6nGI/H1AF67iuvNlJt6EeT1DQNqqo69qBkBtT/ANzCmpZx1xTXAAAAAElFTkSuQmCC&quot;); background-repeat: no-repeat; background-attachment: scroll; background-size: 16px 18px; background-position: 98% 50%; cursor: auto;" autocomplete="off">
                                    <label for="rpassword">Password</label>
                                </div>
                            </div>
                            <a href="#" class="btn filled">Browse Products</a>
                        </div>
                    </div>
            </div>
       </div>
  </div>

</div> -->


<!-- /* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 999; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-content {
  background-color: #fefefe;
  margin: 15% auto; /* 15% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 80%; /* Could be more or less, depending on screen size */
  margin-top: 3%;
}

/* The Close Button */
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
} -->


<!-- // Get the modal
var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
btn.onclick = function() {
  modal.style.display = "block";
}

// When the user clicks on <span> (x), close the modal
span.onclick = function() {
  modal.style.display = "none";
}

// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
} -->


<?php get_footer();
