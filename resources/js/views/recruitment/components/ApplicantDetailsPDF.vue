<template>
</template>

<script>
import { degrees, PDFDocument, rgb, StandardFonts } from 'pdf-lib';
import axios from 'axios';

export default {
  name: 'DialogEditPDF',
  props: [
    "applicant",   
    "educ_attains", 
    "experiences",
    "references",
    "fam_members",
    "dependents"
  ],
  data() {
    return {
      pdfUrl: '/pdf/application.pdf',
    }
  },
  methods: {
    closeDialog() {
      this.$emit('closeDialog');
    },
    previewPDF() {

    },

    async modifyPdf() {
      const url = this.pdfUrl;

      // Fetch the PDF using axios.
      const response = await axios.get(url, { responseType: 'arraybuffer' });
      const existingPdfBytes = response.data;

      // Load the PDF using pdf-lib.
      const pdfDoc = await PDFDocument.load(existingPdfBytes);
      const font = await pdfDoc.embedFont(StandardFonts.TimesRoman);
      const firstPage = pdfDoc.getPages()[0];
      const form = pdfDoc.getForm();
      const checkBox = form.createCheckBox('gender.checkBox.male');
      let textAttr = {
        x: 35,
        y: 740,
        size: 10,
        font: font,
        color: rgb(0, 0, 0),
        rotate: degrees(0),
      };
      let checkboxAttr = { 
        x: 0,
        y: 0,
        width: 5,
        height: 5,
        textColor: rgb(0, 0, 0),
        backgroundColor: rgb(0, 0, 0),
        borderColor: rgb(0, 0, 0),
        borderWidth: 1,
        rotate: degrees(90),
      };

      let data = this.applicant;
      // DATE
      textAttr.x = 60;
      textAttr.y = 788;
      firstPage.drawText(data.date_submitted, textAttr);

      // LAST NAME
      textAttr.x = 44;
      textAttr.y = 740;
      firstPage.drawText(data.lastname, textAttr);

      // FIRST NAME
      textAttr.x = 139;
      textAttr.y = 740;
      firstPage.drawText(data.firstname, textAttr);

      // MIDDLE NAME
      textAttr.x = 230;
      textAttr.y = 740;
      firstPage.drawText(data.middlename, textAttr);

      // POSITION
      textAttr.x = 344;
      textAttr.y = 740;
      firstPage.drawText(data.position_name, textAttr);

      let applicant_age = data.age ? data.age.toString() : '';

      // AGE
      textAttr.x = 473;
      textAttr.y = 742;
      firstPage.drawText(applicant_age, textAttr);
      
      if(data.gender == 'Male')
      {
        checkboxAttr.x = 552;
        checkboxAttr.y = 751;
      }
      else
      {
        checkboxAttr.x = 553;
        checkboxAttr.y = 739;
      }

      checkBox.addToPage(firstPage, checkboxAttr);

      // MOBILE #
      textAttr.x = 495;
      textAttr.y = 722;
      firstPage.drawText(data.contact_no, textAttr);

      // PRESENT ADDRESS
      textAttr.x = 110;
      textAttr.y = 705;
      firstPage.drawText(data.address, textAttr);

      // HOME ADDRESS
      textAttr.x = 110;
      textAttr.y = 690;
      firstPage.drawText(data.address2, textAttr);

      // PLACE OF BIRTH
      textAttr.x = 110;
      textAttr.y = 675;
      firstPage.drawText(data.birth_place, textAttr);

      if(['Single', 'Married'].includes(data.civil_status))
      {
        checkboxAttr.x = 460;
      }

      if(['Widow', 'Domestic Partnership'].includes(data.civil_status))
      {
        checkboxAttr.x = 506;
      }

      if(['Single', 'Widow'].includes(data.civil_status))
      {
        checkboxAttr.y = 695;
      }

      if(['Married', 'Domestic Partnership'].includes(data.civil_status))
      {
        checkboxAttr.y = 683;
      }

      checkBox.addToPage(firstPage, checkboxAttr);
      // // CIVIL STATUS SINGLE
      // checkboxAttr.x = 460;
      // checkboxAttr.y = 695;
      // checkBox.addToPage(firstPage, checkboxAttr);

      // // CIVIL STATUS MARRIED
      // checkboxAttr.x = 460;
      // checkboxAttr.y = 683;
      // checkBox.addToPage(firstPage, checkboxAttr);

      // // CIVIL STATUS WIDOW
      // checkboxAttr.x = 506;
      // checkboxAttr.y = 695;
      // checkBox.addToPage(firstPage, checkboxAttr);

      // // CIVIL STATUS DOMESTIC
      // checkboxAttr.x = 506;
      // checkboxAttr.y = 683;
      // checkBox.addToPage(firstPage, checkboxAttr);

      // DATE OF BIRTH
      textAttr.x = 87;
      textAttr.y = 658;
      firstPage.drawText(data.birthdate || '', textAttr);

      // CITIZENSHIP
      textAttr.x = 203;
      textAttr.y = 658;
      firstPage.drawText(data.citizenship || '', textAttr);

      // RELIGION
      textAttr.x = 360;
      textAttr.y = 658;
      firstPage.drawText(data.religion || '', textAttr);

      // WEIGHT
      let weight = data.weight ? data.weight.toString() + ' kg' : '';
      textAttr.x = 480;
      textAttr.y = 658;
      firstPage.drawText(weight, textAttr);

      // HEIGHT
      let height = data.height ? data.height.toString() + ' cm' : ''; 
      textAttr.x = 545;
      textAttr.y = 658;
      firstPage.drawText(height, textAttr);

      // SSS # 
      textAttr.x = 55;
      textAttr.y = 642;
      firstPage.drawText(data.sss_no || '', textAttr);

      // PHILHEALTH # 
      textAttr.x = 207;
      textAttr.y = 642;
      firstPage.drawText(data.philhealth_no || '', textAttr);

      // PAGIBIG # 
      textAttr.x = 363;
      textAttr.y = 642;
      firstPage.drawText(data.pagibig_no || '', textAttr);

      // TIN # 
      textAttr.x = 467;
      textAttr.y = 642;
      firstPage.drawText(data.tin_no || '', textAttr);

      // HOW LEARN (WALLK-IN)
      if(data.how_learn == 'WALLK-IN')
      {
        checkboxAttr.x = 39;
        checkboxAttr.y = 615;
      }
       
      // HOW LEARN (PRINT ADS/TARPAULIN)
      if(data.how_learn == 'PRINT ADS/TARPAULIN')
      {
        checkboxAttr.x = 93;
        checkboxAttr.y = 615; 
      }
      
      // HOW LEARN (INDEED)
      if(data.how_learn == 'INDEED')
      {
        checkboxAttr.x = 190;
        checkboxAttr.y = 615;
      }
      
      // HOW LEARN (JOB  FAIR)
      if(data.how_learn == 'JOB FAIR')
      {
        checkboxAttr.x = 39;
        checkboxAttr.y = 603;
      }
      
      // HOW LEARN (FACEBOOK)
      if(data.how_learn == 'Addessa FB Page')
      {
        checkboxAttr.x = 93;
        checkboxAttr.y = 603;
      }
      
      // set font size
      textAttr.size = 8;

      // HOW LEARN (OTHERS TEXT)
      if(['Local Government FB Page', 'Now hiring FB Group Page', 'PESO FB Page', 'Addessa Website'].includes(data.how_learn))
      { 
        // HOW LEARN (OTHERS)
        checkboxAttr.x = 190;
        checkboxAttr.y = 603;

        textAttr.x = 225;
        textAttr.y = 603;
        firstPage.drawText(data.how_learn, textAttr); 
      }

      checkBox.addToPage(firstPage, checkboxAttr); 
      
      
      // // HOW LEARN (EMPLOYEE REFFERAL)
      // checkboxAttr.x = 39;
      // checkboxAttr.y = 590;
      // checkBox.addToPage(firstPage, checkboxAttr);  

      // // HOW LEARN (EMPLOYEE REFFERAL TEXT)
      // textAttr.x = 129;
      // textAttr.y = 591;
      // firstPage.drawText('Refered by Anokaya', textAttr);

      // set font size
      textAttr.size = 7;

      // EMAIL ADD 
      textAttr.x = 363;
      textAttr.y = 626;
      firstPage.drawText(data.email || '', textAttr);

      // // FACEBOOK
      // textAttr.x = 490;
      // textAttr.y = 626;
      // firstPage.drawText('xavier.aguilar.deguzman', textAttr);

      // checkboxAttr.width = 3,
      // checkboxAttr.height = 3,
      // textAttr.size = 8;

      // // HAS RELATIVES (YES)
      // checkboxAttr.x = 359;
      // checkboxAttr.y = 601;
      // checkBox.addToPage(firstPage, checkboxAttr); 

      // // HAS RELATIVES (YES TEXT)
      // textAttr.x = 343;
      // textAttr.y = 592;
      // firstPage.drawText('Anokaya Anokaya', textAttr);

      // // HAS RELATIVES (NO)
      // checkboxAttr.x = 380;
      // checkboxAttr.y = 601;
      // checkBox.addToPage(firstPage, checkboxAttr);

      // // INVOLVED IN CRIMINAL ACTIVITIES (YES)
      // checkboxAttr.x = 505;
      // checkboxAttr.y = 601;
      // checkBox.addToPage(firstPage, checkboxAttr); 

      // INVOLVED IN CRIMINAL ACTIVITIES (YES TEXT)
      // textAttr.x = 475;
      // textAttr.y = 592;
      // firstPage.drawText('Anokaya Anokaya', textAttr);

      // // INVOLVED IN CRIMINAL ACTIVITIES (NO)
      // checkboxAttr.x = 526;
      // checkboxAttr.y = 601;
      // checkBox.addToPage(firstPage, checkboxAttr); 

      let junior_school = '';
      let junior_course = '';
      let junior_major = '';
      let junior_sy_attended = '';
      let junior_honors = '';
      let senior_school = '';
      let senior_course = '';
      let senior_major = '';
      let senior_sy_attended = '';
      let senior_honors = '';
      let vocational_school = '';
      let vocational_course = '';
      let vocational_major = '';
      let vocational_sy_attended = '';
      let vocational_honors = '';
      let college_school = '';
      let college_course = '';
      let college_major = '';
      let college_sy_attended = '';
      let college_honors = '';

      this.educ_attains.forEach(value => {
        if(['HighSchool', 'Junior HighSchool'].includes(value.educ_level))
        {
          junior_school = value.school;
          junior_course = value.course;
          junior_major = value.major;
          junior_honors = value.sy_honors;

          let [fr, to] = value.sy_attended.split(' to ');
          junior_sy_attended = `${fr}-${to}`;
          
        }

        if(value.educ_level == 'Senior HighSchool')
        {
          senior_school = value.school;
          senior_course = value.course;
          senior_major = value.major;
          senior_honors = value.sy_honors;

          let [fr, to] = value.sy_attended.split(' to ');
          senior_sy_attended = `${fr}-${to}`
        }

        if(value.educ_level == 'Vocational School')
        {
          vocational_school = value.school;
          vocational_course = value.course;
          vocational_major = value.major;
          vocational_honors = value.sy_honors;

          let [fr, to] = value.sy_attended.split(' to ');
          vocational_sy_attended = `${fr}-${to}`
        }

        if(value.educ_level == 'College')
        {
          college_school = value.school;
          college_course = value.course;
          college_major = value.major;
          college_honors = value.sy_honors;

          let [fr, to] = value.sy_attended.split(' to ');
          college_sy_attended = `${fr}-${to}`
        }

      });

      // SCHOOL JUNIOR
      textAttr.x = 117;
      textAttr.y = 542;
      textAttr.size = 8;
      firstPage.drawText(junior_school || '', textAttr);

      // COURSE JUNIOR
      textAttr.x = 252;
      textAttr.y = 542;
      textAttr.size = 8;
      firstPage.drawText(junior_course || '', textAttr);

      // MAJOR JUNIOR
      textAttr.x = 387;
      textAttr.y = 542;
      textAttr.size = 8;
      firstPage.drawText(junior_major || '', textAttr);

      // SY JUNIOR
      textAttr.x = 461;
      textAttr.y = 542;
      textAttr.size = 6;
      firstPage.drawText(junior_sy_attended || '', textAttr);

      // HONOR JUNIOR
      textAttr.x = 520;
      textAttr.y = 542;
      textAttr.size = 7;
      firstPage.drawText(junior_honors || '', textAttr);

      // SCHOOL SENIOR
      textAttr.x = 117;
      textAttr.y = 523;
      textAttr.size = 8;
      firstPage.drawText(senior_school || '', textAttr);

      // COURSE SENIOR
      textAttr.x = 252;
      textAttr.y = 523;
      textAttr.size = 8;
      firstPage.drawText(senior_course || '', textAttr);

      // MAJOR SENIOR
      textAttr.x = 387;
      textAttr.y = 523;
      textAttr.size = 8;
      firstPage.drawText(senior_major || '', textAttr);

      // SY SENIOR
      textAttr.x = 461;
      textAttr.y = 523;
      textAttr.size = 6;
      firstPage.drawText(senior_sy_attended || '', textAttr);

      // HONOR SENIOR
      textAttr.x = 520;
      textAttr.y = 523;
      textAttr.size = 7;
      firstPage.drawText(senior_honors || '', textAttr);

      // SCHOOL COLLEGE
      textAttr.x = 117;
      textAttr.y = 505;
      textAttr.size = 8;
      firstPage.drawText(college_school || '', textAttr);

      // COURSE COLLEGE
      textAttr.x = 252;
      textAttr.y = 505;
      textAttr.size = 8;
      firstPage.drawText(college_course || '', textAttr);

      // MAJOR COLLEGE
      textAttr.x = 387;
      textAttr.y = 505;
      textAttr.size = 8;
      firstPage.drawText(college_major || '', textAttr);

      // SY COLLEGE
      textAttr.x = 461;
      textAttr.y = 505;
      textAttr.size = 6;
      firstPage.drawText(college_sy_attended || '', textAttr);

      // HONOR COLLEGE
      textAttr.x = 520;
      textAttr.y = 505;
      textAttr.size = 7;
      firstPage.drawText(college_honors || '', textAttr);

      // SCHOOL VOCATIONAL
      textAttr.x = 117;
      textAttr.y = 488;
      textAttr.size = 8;
      firstPage.drawText(vocational_school || '', textAttr);

      // COURSE VOCATIONAL
      textAttr.x = 252;
      textAttr.y = 488;
      textAttr.size = 8;
      firstPage.drawText(vocational_course || '', textAttr);

      // MAJOR VOCATIONAL
      textAttr.x = 387;
      textAttr.y = 488;
      textAttr.size = 8;
      firstPage.drawText(vocational_major || '', textAttr);

      // SY VOCATIONAL
      textAttr.x = 461;
      textAttr.y = 488;
      textAttr.size = 6;
      firstPage.drawText(vocational_sy_attended || '', textAttr);

      // HONOR VOCATIONAL
      textAttr.x = 520;
      textAttr.y = 488;
      textAttr.size = 7;
      firstPage.drawText(vocational_honors || '', textAttr);

      textAttr.size = 8;
      
      // EMPLOYMENT HISTORY 1 
      if(this.experiences.length > 0)
      {
        let exp1 = this.experiences[0];
        // EMPLOYMENT HISTORY 1 NAME
        textAttr.x = 33;
        textAttr.y = 441;
        firstPage.drawText(exp1.employer || '', textAttr);

        // EMPLOYMENT HISTORY 1 POSITION
        textAttr.x = 145;
        textAttr.y = 441;
        firstPage.drawText(exp1.position || '', textAttr);

        // EMPLOYMENT HISTORY 1 SALARY
        textAttr.x = 252;
        textAttr.y = 441;
        firstPage.drawText(exp1.salary || '', textAttr);

        // EMPLOYMENT HISTORY 1 DATE SERVICE
        textAttr.x = 310;
        textAttr.y = 441;
        firstPage.drawText(exp1.date_of_service || '', textAttr);

        // EMPLOYMENT HISTORY 1 JOB DESCRIPTION
        textAttr.x = 387;
        textAttr.y = 441;
        firstPage.drawText(exp1.job_description || '', textAttr);
      }
      
      // EMPLOYMENT HISTORY 2 
      if(this.experiences.length > 1)
      { 
        let exp2 = this.experiences[1];
        // EMPLOYMENT HISTORY 2 NAME
        textAttr.x = 33;
        textAttr.y = 425;
        firstPage.drawText(exp2.employer || '', textAttr);

        // EMPLOYMENT HISTORY 2 POSITION
        textAttr.x = 145;
        textAttr.y = 425;
        firstPage.drawText(exp2.position || '', textAttr);

        // EMPLOYMENT HISTORY 2 SALARY
        textAttr.x = 252;
        textAttr.y = 425;
        firstPage.drawText(exp2.salary || '', textAttr);

        // EMPLOYMENT HISTORY 2 DATE SERVICE
        textAttr.x = 310;
        textAttr.y = 425;
        firstPage.drawText(exp2.date_of_service || '', textAttr);

        // EMPLOYMENT HISTORY 2 JOB DESCRIPTION
        textAttr.x = 387;
        textAttr.y = 425;
        firstPage.drawText(exp2.job_description || '', textAttr);
      }

      // EMPLOYMENT HISTORY 3
      if(this.experiences.length > 2)
      { 
        let exp3 = this.experiences[2];
        // EMPLOYMENT HISTORY 3 NAME
        textAttr.x = 33;
        textAttr.y = 409;
        firstPage.drawText(exp3.employer || '', textAttr);

        // EMPLOYMENT HISTORY 3 POSITION
        textAttr.x = 145;
        textAttr.y = 409;
        firstPage.drawText(exp3.position || '', textAttr);

        // EMPLOYMENT HISTORY 3 SALARY
        textAttr.x = 252;
        textAttr.y = 409;
        firstPage.drawText(exp3.salary || '', textAttr);

        // EMPLOYMENT HISTORY 3 DATE SERVICE
        textAttr.x = 310;
        textAttr.y = 409;
        firstPage.drawText(exp3.date_of_service || '', textAttr);

        // EMPLOYMENT HISTORY 3 JOB DESCRIPTION
        textAttr.x = 387;
        textAttr.y = 409;
        firstPage.drawText(exp3.job_description || '', textAttr);
      }

      if(this.references.length > 0)
      { 

        let ref1 = this.references[0];

        // REFERENCE 1 NAME
        textAttr.x = 33;
        textAttr.y = 363;
        firstPage.drawText(ref1.name || '', textAttr);

        // REFERENCE 1 ADDRESS
        textAttr.x = 176;
        textAttr.y = 363;
        firstPage.drawText(ref1.address || '', textAttr);

        // REFERENCE 1 CONTACT
        textAttr.x = 309;
        textAttr.y = 363;
        firstPage.drawText(ref1.contact || '', textAttr);

        // REFERENCE 1 COMPANY
        textAttr.x = 387;
        textAttr.y = 363;
        firstPage.drawText(ref1.company || '', textAttr);

        // REFERENCE 1 POSITION
        textAttr.x = 500;
        textAttr.y = 363;
        firstPage.drawText(ref1.position || '', textAttr);

      }
      
      if(this.references.length > 1)
      {
        let ref2 = this.references[1];

        // REFERENCE 2 NAME
        textAttr.x = 33;
        textAttr.y = 347;
        firstPage.drawText(ref2.name || '', textAttr);

        // REFERENCE 2 ADDRESS
        textAttr.x = 176;
        textAttr.y = 347;
        firstPage.drawText(ref2.address || '', textAttr);

        // REFERENCE 2 CONTACT
        textAttr.x = 309;
        textAttr.y = 347;
        firstPage.drawText(ref2.contact || '', textAttr);

        // REFERENCE 2 COMPANY
        textAttr.x = 387;
        textAttr.y = 347;
        firstPage.drawText(ref2.company || '', textAttr);

        // REFERENCE 2 POSITION
        textAttr.x = 500;
        textAttr.y = 347;
        firstPage.drawText(ref2.position || '', textAttr);
      }

      if(this.references.length > 2)
      {
        let ref3 = this.references[2];

        // REFERENCE 3 NAME
        textAttr.x = 33;
        textAttr.y = 331;
        firstPage.drawText(ref3.name || '', textAttr);

        // REFERENCE 3 ADDRESS
        textAttr.x = 176;
        textAttr.y = 331;
        firstPage.drawText(ref3.address || '', textAttr);

        // REFERENCE 3 CONTACT
        textAttr.x = 309;
        textAttr.y = 331;
        firstPage.drawText(ref3.contact || '', textAttr);

        // REFERENCE 3 COMPANY
        textAttr.x = 387;
        textAttr.y = 331;
        firstPage.drawText(ref3.company || '', textAttr);

        // REFERENCE 3 POSITION
        textAttr.x = 500;
        textAttr.y = 331;
        firstPage.drawText(ref3.position || '', textAttr);
      }

      let father = '';
      let mother = '';
      let spouse = '';
      let guardian = '';
    
      this.fam_members.forEach(value => {
        if(value.relationship == 'father')
        {
          father = value;
        }
        else if(value.relationship == 'mother')
        {
          mother = value;
        }
        else if(value.relationship == 'spouse')
        {
          spouse = value;
        }
        else if(value.relationship == 'guardian')
        {
          guardian = value;
        }
      });

      // FATHER NAME
      textAttr.x = 90;
      textAttr.y = 283;
      firstPage.drawText(father.name || '', textAttr);

      let father_age = father.age ? father.age.toString() : ''; 

      // FATHER AGE
      textAttr.x = 223;
      textAttr.y = 283;
      firstPage.drawText(father_age || '', textAttr);

      // FATHER ADDRESS
      textAttr.x = 253;
      textAttr.y = 283;
      firstPage.drawText(father.address || '', textAttr);

      // FATHER CONTACT
      textAttr.x = 403;
      textAttr.y = 283;
      firstPage.drawText(father.contact || '', textAttr);

      // FATHER OCCUPATION
      textAttr.x = 480;
      textAttr.y = 283;
      firstPage.drawText(father.occupation || '', textAttr);

      // MOTHER NAME
      textAttr.x = 90;
      textAttr.y = 265;
      firstPage.drawText(mother.occupation || '', textAttr);

      let mother_age = mother.age ? mother.age.toString() : '';

      // MOTHER AGE
      textAttr.x = 223;
      textAttr.y = 265;
      firstPage.drawText(mother_age || '', textAttr);

      // MOTHER ADDRESS
      textAttr.x = 253;
      textAttr.y = 265;
      firstPage.drawText(mother.address || '', textAttr);

      // MOTHER CONTACT
      textAttr.x = 403;
      textAttr.y = 265;
      firstPage.drawText(mother.contact || '', textAttr);

      // MOTHER OCCUPATION
      textAttr.x = 480;
      textAttr.y = 265;
      firstPage.drawText(mother.occupation || '', textAttr);

      let spouse_age = spouse.age ? spouse.age.toString() : '';

      // SPOUSE NAME
      textAttr.x = 90;
      textAttr.y = 248;
      firstPage.drawText(spouse.name || '', textAttr);

      // SPOUSE AGE
      textAttr.x = 223;
      textAttr.y = 248;
      firstPage.drawText(spouse_age || '', textAttr);

      // SPOUSE ADDRESS
      textAttr.x = 253;
      textAttr.y = 248;
      firstPage.drawText(spouse.address || '', textAttr);

      // SPOUSE CONTACT
      textAttr.x = 403;
      textAttr.y = 248;
      firstPage.drawText(spouse.contact || '', textAttr);

      // SPOUSE OCCUPATION
      textAttr.x = 480;
      textAttr.y = 248;
      firstPage.drawText(spouse.occupation || '', textAttr);

      // GUARDIAN NAME
      textAttr.x = 90;
      textAttr.y = 230;
      firstPage.drawText(guardian.name || '', textAttr);

      let guardian_age = guardian.age ? guardian.age.toString() : '';

      // GUARDIAN AGE
      textAttr.x = 223;
      textAttr.y = 230;
      firstPage.drawText(guardian_age || '', textAttr);

      // GUARDIAN ADDRESS
      textAttr.x = 253;
      textAttr.y = 230;
      firstPage.drawText(guardian.address || '', textAttr);

      // GUARDIAN CONTACT
      textAttr.x = 403;
      textAttr.y = 230;
      firstPage.drawText(guardian.contact || '', textAttr);

      // GUARDIAN OCCUPATION
      textAttr.x = 480;
      textAttr.y = 230;
      firstPage.drawText(guardian.occupation || '', textAttr);

      // DEPENDENT 1
      if(this.dependents.length > 0)
      {
        let dep1 = this.dependents[0];
        // DEPENDENT 1 NAME
        textAttr.x = 33;
        textAttr.y = 183;
        firstPage.drawText(dep1.name || '', textAttr);

        // DEPENDENT 1 RELATIONSHIP
        textAttr.x = 176;
        textAttr.y = 183;
        firstPage.drawText(dep1.relationship || '', textAttr);

        let dep1_age = dep1.age ? dep1.age.toString() : '';

        // DEPENDENT 1 AGE
        textAttr.x = 273;
        textAttr.y = 183;
        firstPage.drawText(dep1_age || '', textAttr);

        // DEPENDENT 1 ADDRESS
        textAttr.x = 303;
        textAttr.y = 183;
        firstPage.drawText(dep1.address || '', textAttr);

        // DEPENDENT 1 OCCUPATION
        textAttr.x = 480;
        textAttr.y = 183;
        firstPage.drawText(dep1.occupation || '', textAttr);
      }

      // DEPENDENT 2
      if(this.dependents.length > 1)
      {
        let dep2 = this.dependents[1];
        // DEPENDENT 2 NAME
        textAttr.x = 33;
        textAttr.y = 167;
        firstPage.drawText(dep2.name || '', textAttr);

        // DEPENDENT 2 RELATIONSHIP
        textAttr.x = 176;
        textAttr.y = 167;
        firstPage.drawText(dep2.relationship || '', textAttr);

        let dep2_age = dep2.age ? dep2.age.toString() : '';

        // DEPENDENT 2 AGE
        textAttr.x = 273;
        textAttr.y = 167;
        firstPage.drawText(dep2_age || '', textAttr);

        // DEPENDENT 2 ADDRESS
        textAttr.x = 303;
        textAttr.y = 167;
        firstPage.drawText(dep2.address || '', textAttr);

        // DEPENDENT 2 OCCUPATION
        textAttr.x = 480;
        textAttr.y = 167;
        firstPage.drawText(dep2.occupation || '', textAttr);

      }

      // DEPENDENT 3
      if(this.dependents.length > 2)
      {
        let dep3 = this.dependents[2];
        // DEPENDENT 3 NAME
        textAttr.x = 33;
        textAttr.y = 151;
        firstPage.drawText(dep3.name || '', textAttr);

        // DEPENDENT 3 RELATIONSHIP
        textAttr.x = 176;
        textAttr.y = 151;
        firstPage.drawText(dep3.relationship || '', textAttr);

        let dep3_age = dep3.age ? dep3.age.toString() : '';

        // DEPENDENT 3 AGE
        textAttr.x = 273;
        textAttr.y = 151;
        firstPage.drawText(dep3_age || '', textAttr);

        // DEPENDENT 3 ADDRESS
        textAttr.x = 303;
        textAttr.y = 151;
        firstPage.drawText(dep3.address || '', textAttr);

        // DEPENDENT 3 OCCUPATION
        textAttr.x = 480;
        textAttr.y = 151;
        firstPage.drawText(dep3.occupation || '', textAttr);
      }

      // Save the modified PDF to a variable (`pdfBytes`).
      const pdfBytes = await pdfDoc.save();

      // Now, you can use `pdfBytes` for any additional operations (e.g. downloading the modified PDF).
      return pdfBytes;
    },

    async handleClickDownload() {
      try {
        const pdfBytes = await this.modifyPdf();
        this.downloadPdf(pdfBytes);
      } catch (error) {
        console.error('Error modifying PDF:', error);
      }
    },

    downloadPdf(pdfBytes) {
      const blob = new Blob([pdfBytes], { type: 'application/pdf' });
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = 'application_form.pdf';
      link.click();
    },
  },

  mounted() {
    // this.handleClickDownload();
    // console.log(this.data);
  }
}
</script>
