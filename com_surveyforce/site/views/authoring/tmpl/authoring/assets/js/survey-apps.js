var $ = jQuery,
    currQuestion, fileInterval = null,
    file = {},
    firstSave = !1,
    BULKS = [{
        type: "vertical",
        values: [{
            fr: "Under 18",
            en: "Under 18"
        }, {
            fr: "18-24",
            en: "18-24"
        }, {
            fr: "25-34",
            en: "25-34"
        }, {
            fr: "35-44",
            en: "35-44"
        }, {
            fr: "45-54",
            en: "45-54"
        }, {
            fr: "55-64",
            en: "55-64"
        }, {
            fr: "65 or Above",
            en: "65 or Above"
        }, {
            fr: "Prefer Not to Answer",
            en: "Prefer Not to Answer"
        }],
        name: "Age"
    }, {
        type: "vertical",
        values: [{
            fr: "Employed Full-Time",
            en: "Employed Full-Time"
        }, {
            fr: "Employed Part-Time",
            en: "Employed Part-Time"
        }, {
            fr: "Self-employed",
            en: "Self-employed"
        }, {
            fr: "Not employed, but looking for work",
            en: "Not employed, but looking for work"
        }, {
            fr: "Not employed and not looking for work",
            en: "Not employed and not looking for work"
        }, {
            fr: "Homemaker",
            en: "Homemaker"
        }, {
            fr: "Retired",
            en: "Retired"
        }, {
            fr: "Student",
            en: "Student"
        }, {
            fr: "Prefer Not to Answer",
            en: "Prefer Not to Answer"
        }],
        name: "Employment"
    }, {
        type: "vertical",
        values: [{
            fr: "Under $20,000",
            en: "Under $20,000"
        }, {
            fr: "$20,000 - $30,000",
            en: "$20,000 - $30,000"
        }, {
            fr: "$30,000 - $40,000",
            en: "$30,000 - $40,000"
        }, {
            fr: "$40,000 - $50,000",
            en: "$40,000 - $50,000"
        }, {
            fr: "$50,000 - $75,000",
            en: "$50,000 - $75,000"
        }, {
            fr: "$75,000 - $100,000",
            en: "$75,000 - $100,000"
        }, {
            fr: "$100,000 - $150,000",
            en: "$100,000 - $150,000"
        }, {
            fr: "$150,000 or more",
            en: "$150,000 or more"
        }, {
            fr: "Prefer Not to Answer",
            en: "Prefer Not to Answer"
        }],
        name: "Income Level"
    }, {
        type: "horizontal",
        values: [{
            fr: "Some High School",
            en: "Some High School"
        }, {
            fr: "High School Graduate or Equivalent",
            en: "High School Graduate or Equivalent"
        }, {
            fr: "Trade or Vocational Degree",
            en: "Trade or Vocational Degree"
        }, {
            fr: "Some College",
            en: "Some College"
        }, {
            fr: "Associate Degree",
            en: "Associate Degree"
        }, {
            fr: "Bachelor's Degree",
            en: "Bachelor's Degree"
        }, {
            fr: "Graduate of Professional Degree",
            en: "Graduate of Professional Degree"
        }, {
            fr: "Prefer Not to Answer",
            en: "Prefer Not to Answer"
        }],
        name: "Education"
    }, {
        type: "vertical",
        values: [{
            fr: "Single, Never Married",
            en: "Single, Never Married"
        }, {
            fr: "Married",
            en: "Married"
        }, {
            fr: "Living with Partner",
            en: "Living with Partner"
        }, {
            fr: "Separated",
            en: "Separated"
        }, {
            fr: "Divorced",
            en: "Divorced"
        }, {
            fr: "Widowed",
            en: "Widowed"
        }, {
            fr: "Prefer Not to Answer",
            en: "Prefer Not to Answer"
        }],
        name: "Marital Status"
    }, {
        type: "vertical",
        values: [{
            fr: "White / Caucasian",
            en: "White / Caucasian"
        }, {
            fr: "Spanish / Hispanic / Latino",
            en: "Spanish / Hispanic / Latino"
        }, {
            fr: "Black / African American",
            en: "Black / African American"
        }, {
            fr: "Asian",
            en: "Asian"
        }, {
            fr: "Pacific Islander",
            en: "Pacific Islander"
        }, {
            fr: "Native American",
            en: "Native American"
        }, {
            fr: "Other",
            en: "Other"
        }, {
            fr: "Prefer Not to Answer",
            en: "Prefer Not to Answer"
        }],
        name: "Race"
    }, {
        type: "vertical",
        values: [{
            fr: "janvier",
            en: "January"
        }, {
            fr: "f\u00e9vrier",
            en: "February"
        }, {
            fr: "mars",
            en: "March"
        }, {
            fr: "avril",
            en: "April"
        }, {
            fr: "mai",
            en: "May"
        }, {
            fr: "juin",
            en: "June"
        }, {
            fr: "juillet",
            en: "July"
        }, {
            fr: "ao\u00fbt",
            en: "August"
        }, {
            fr: "septembre",
            en: "September"
        }, {
            fr: "octobre",
            en: "October"
        }, {
            fr: "novembre",
            en: "November"
        }, {
            fr: "d\u00e9cembre",
            en: "December"
        }],
        name: "Months"
    }, {
        type: "vertical",
        values: [{
            fr: "lundi",
            en: "Monday"
        }, {
            fr: "mardi",
            en: "Tuesday"
        }, {
            fr: "mercredi",
            en: "Wednesday"
        }, {
            fr: "jeudi",
            en: "Thursday"
        }, {
            fr: "vendredi",
            en: "Friday"
        }, {
            fr: "samedi",
            en: "Saturday"
        }, {
            fr: "dimanche",
            en: "Sunday"
        }],
        name: "Days"
    }, {
        type: "vertical",
        values: [{
            fr: "Alberta",
            en: "Alberta"
        }, {
            fr: "Colombie-Britannique",
            en: "British Columbia"
        }, {
            fr: "Manitoba",
            en: "Manitoba"
        }, {
            fr: "Nouveau-Brunswick",
            en: "New Brunswick"
        }, {
            fr: "Terre-Neuve-et-Labrador",
            en: "Newfoundland and Labrador"
        }, {
            fr: "Territoires du Nord-Ouest",
            en: "Northwest Territories"
        }, {
            fr: "Nouvelle-\u00c9cosse",
            en: "Nova Scotia"
        }, {
            fr: "Nunavut",
            en: "Nunavut"
        }, {
            fr: "Ontario",
            en: "Ontario"
        }, {
            fr: "l'\u00eele du Prince-\u00c9douard",
            en: "Prince Edward Island"
        }, {
            fr: "Qu\u00e9bec",
            en: "Quebec"
        }, {
            fr: "Saskatchewan",
            en: "Saskatchewan"
        }, {
            fr: "Yukon",
            en: "Yukon"
        }],
        name: "Canadian Provinces"
    }, {
        type: "vertical",
        values: [{
            fr: "Alabama",
            en: "Alabama"
        }, {
            fr: "Alaska",
            en: "Alaska"
        }, {
            fr: "Arizona",
            en: "Arizona"
        }, {
            fr: "Arkansas",
            en: "Arkansas"
        }, {
            fr: "Californie",
            en: "California"
        }, {
            fr: "Colorado",
            en: "Colorado"
        }, {
            fr: "Connecticut",
            en: "Connecticut"
        }, {
            fr: "Delaware",
            en: "Delaware"
        }, {
            fr: "Floride",
            en: "Florida"
        }, {
            fr: "G\u00e9orgie",
            en: "Georgia"
        }, {
            fr: "Hawa\u00ef",
            en: "Hawaii"
        }, {
            fr: "Idaho",
            en: "Idaho"
        }, {
            fr: "Illinois",
            en: "Illinois"
        }, {
            fr: "Indiana",
            en: "Indiana"
        }, {
            fr: "Iowa",
            en: "Iowa"
        }, {
            fr: "Kansas",
            en: "Kansas"
        }, {
            fr: "Kentucky",
            en: "Kentucky"
        }, {
            fr: "Louisiane",
            en: "Louisiana"
        }, {
            fr: "Maine",
            en: "Maine"
        }, {
            fr: "Mariland",
            en: "Maryland"
        }, {
            fr: "Massachusetts",
            en: "Massachusetts"
        }, {
            fr: "Michigan",
            en: "Michigan"
        }, {
            fr: "Minnesota",
            en: "Minnesota"
        }, {
            fr: "Mississippi",
            en: "Mississippi"
        }, {
            fr: "Missouri",
            en: "Missouri"
        }, {
            fr: "Montana",
            en: "Montana"
        }, {
            fr: "Nebraska",
            en: "Nebraska"
        }, {
            fr: "Nevada",
            en: "Nevada"
        }, {
            fr: "New Hampshire",
            en: "New Hampshire"
        }, {
            fr: "New Jersey",
            en: "New Jersey"
        }, {
            fr: "Nouveau-Mexique",
            en: "New Mexico"
        }, {
            fr: "New-York",
            en: "New York"
        }, {
            fr: "Caroline du Nord",
            en: "North Carolina"
        }, {
            fr: "Dakota du Nord",
            en: "North Dakota"
        }, {
            fr: "Ohio",
            en: "Ohio"
        }, {
            fr: "Oklahoma",
            en: "Oklahoma"
        }, {
            fr: "Oregon",
            en: "Oregon"
        }, {
            fr: "Pennsylvanie",
            en: "Pennsylvania"
        }, {
            fr: "Rhode Island",
            en: "Rhode Island"
        }, {
            fr: "Caroline du Sud",
            en: "South Carolina"
        }, {
            fr: "Dakota du Sud",
            en: "South Dakota"
        }, {
            fr: "Tennessee",
            en: "Tennessee"
        }, {
            fr: "Texas",
            en: "Texas"
        }, {
            fr: "Utah",
            en: "Utah"
        }, {
            fr: "Vermont",
            en: "Vermont"
        }, {
            fr: "Virginie",
            en: "Virginia"
        }, {
            fr: "Washington",
            en: "Washington"
        }, {
            fr: "Virginie Occidentale",
            en: "West Virginia"
        }, {
            fr: "Wisconsin",
            en: "Wisconsin"
        }, {
            fr: "Wyoming",
            en: "Wyoming"
        }],
        name: "US States"
    }, {
        type: "vertical",
        values: [{
            fr: "Afghanistan",
            en: "Afghanistan"
        }, {
            fr: "Albanie",
            en: "Albania"
        }, {
            fr: "Alg\u00e9rie",
            en: "Algeria"
        }, {
            fr: "Andorre",
            en: "Andorra"
        }, {
            fr: "Angola",
            en: "Angola"
        }, {
            fr: "Antarctique",
            en: "Antarctica"
        }, {
            fr: "Antigua and Barbuda",
            en: "Antigua and Barbuda"
        }, {
            fr: "Argentine",
            en: "Argentina"
        }, {
            fr: "Arm\u00e9nie",
            en: "Armenia"
        }, {
            fr: "Australie",
            en: "Australia"
        }, {
            fr: "Autriche",
            en: "Austria"
        }, {
            fr: "Azerba\u00efdjan",
            en: "Azerbaijan"
        }, {
            fr: "Bahamas",
            en: "Bahamas"
        }, {
            fr: "Bahre\u00efn",
            en: "Bahrain"
        }, {
            fr: "Bangladesh",
            en: "Bangladesh"
        }, {
            fr: "La Barbade",
            en: "Barbados"
        }, {
            fr: "Bi\u00e9lorussie",
            en: "Belarus"
        }, {
            fr: "Belgique",
            en: "Belgium"
        }, {
            fr: "B\u00e9lize",
            en: "Belize"
        }, {
            fr: "B\u00e9nin",
            en: "Benin"
        }, {
            fr: "Bermudes",
            en: "Bermuda"
        }, {
            fr: "Boutan",
            en: "Bhutan"
        }, {
            fr: "Bolivie",
            en: "Bolivia"
        }, {
            fr: "Bosnie-Herz\u00e9govine",
            en: "Bosnia and Herzegovina"
        }, {
            fr: "Botswana",
            en: "Botswana"
        }, {
            fr: "Br\u00e9sil",
            en: "Brazil"
        }, {
            fr: "Brunei",
            en: "Brunei"
        }, {
            fr: "Bulgarie",
            en: "Bulgaria"
        }, {
            fr: "Burkina Faso",
            en: "Burkina Faso"
        }, {
            fr: "Burma",
            en: "Burma"
        }, {
            fr: "Burundi",
            en: "Burundi"
        }, {
            fr: "Cambodge",
            en: "Cambodia"
        }, {
            fr: "Cameroun",
            en: "Cameroon"
        }, {
            fr: "Canada",
            en: "Canada"
        }, {
            fr: "Cap-Vert",
            en: "Cape Verde"
        }, {
            fr: "R\u00e9publique Centrafricaine",
            en: "Central African Republic"
        }, {
            fr: "Tchad",
            en: "Chad"
        }, {
            fr: "Chili",
            en: "Chile"
        }, {
            fr: "Chine",
            en: "China"
        }, {
            fr: "Colombie",
            en: "Colombia"
        }, {
            fr: "Comores",
            en: "Comoros"
        }, {
            fr: "Congo, Democratic Republic",
            en: "Congo, Democratic Republic"
        }, {
            fr: "Congo, Republic of the",
            en: "Congo, Republic of the"
        }, {
            fr: "Costa Rica",
            en: "Costa Rica"
        }, {
            fr: "Cote d'Ivoire",
            en: "Cote d'Ivoire"
        }, {
            fr: "Croatie",
            en: "Croatia"
        }, {
            fr: "Cuba",
            en: "Cuba"
        }, {
            fr: "Chypre",
            en: "Cyprus"
        }, {
            fr: "R\u00e9publique tch\u00e8que",
            en: "Czech Republic"
        }, {
            fr: "Danemark",
            en: "Denmark"
        }, {
            fr: "Djibouti",
            en: "Djibouti"
        }, {
            fr: "Dominique",
            en: "Dominica"
        }, {
            fr: "R\u00e9publique Dominicaine",
            en: "Dominican Republic"
        }, {
            fr: "Timor Oriental",
            en: "East Timor"
        }, {
            fr: "\u00c9quateur",
            en: "Ecuador"
        }, {
            fr: "\u00c9gypte",
            en: "Egypt"
        }, {
            fr: "Salvador",
            en: "El Salvador"
        }, {
            fr: "Guin\u00e9e \u00c9quatoriale",
            en: "Equatorial Guinea"
        }, {
            fr: "\u00c9rythr\u00e9e",
            en: "Eritrea"
        }, {
            fr: "Estonie",
            en: "Estonia"
        }, {
            fr: "Ethiopie",
            en: "Ethiopia"
        }, {
            fr: "Fidji",
            en: "Fiji"
        }, {
            fr: "Finlande",
            en: "Finland"
        }, {
            fr: "France",
            en: "France"
        }, {
            fr: "Gabon",
            en: "Gabon"
        }, {
            fr: "Gambie",
            en: "Gambia"
        }, {
            fr: "G\u00e9orgie",
            en: "Georgia"
        }, {
            fr: "Allemagne",
            en: "Germany"
        }, {
            fr: "Ghana",
            en: "Ghana"
        }, {
            fr: "Gr\u00e8ce",
            en: "Greece"
        }, {
            fr: "Groenland",
            en: "Greenland"
        }, {
            fr: "Grenade",
            en: "Grenada"
        }, {
            fr: "Guatemala",
            en: "Guatemala"
        }, {
            fr: "Guin\u00e9e",
            en: "Guinea"
        }, {
            fr: "Guin\u00e9e-Bissau",
            en: "Guinea-Bissau"
        }, {
            fr: "Guyanne",
            en: "Guyana"
        }, {
            fr: "Ha\u00efti",
            en: "Haiti"
        }, {
            fr: "Honduras",
            en: "Honduras"
        }, {
            fr: "Hong-Kong",
            en: "Hong Kong"
        }, {
            fr: "Hongrie",
            en: "Hungary"
        }, {
            fr: "Islande",
            en: "Iceland"
        }, {
            fr: "Inde",
            en: "India"
        }, {
            fr: "Indon\u00e9sie",
            en: "Indonesia"
        }, {
            fr: "Iran",
            en: "Iran"
        }, {
            fr: "Irak",
            en: "Iraq"
        }, {
            fr: "Irlande",
            en: "Ireland"
        }, {
            fr: "Isra\u00ebl",
            en: "Israel"
        }, {
            fr: "Italie",
            en: "Italy"
        }, {
            fr: "Jama\u00efque",
            en: "Jamaica"
        }, {
            fr: "Japon",
            en: "Japan"
        }, {
            fr: "Jordanie",
            en: "Jordan"
        }, {
            fr: "Kazakhstan",
            en: "Kazakhstan"
        }, {
            fr: "Kenya",
            en: "Kenya"
        }, {
            fr: "Kiribati",
            en: "Kiribati"
        }, {
            fr: "Korea, North",
            en: "Korea, North"
        }, {
            fr: "Korea, South",
            en: "Korea, South"
        }, {
            fr: "Kowe\u00eft",
            en: "Kuwait"
        }, {
            fr: "Kirghizistan",
            en: "Kyrgyzstan"
        }, {
            fr: "Laos",
            en: "Laos"
        }, {
            fr: "Lettonie",
            en: "Latvia"
        }, {
            fr: "Liban",
            en: "Lebanon"
        }, {
            fr: "Lesotho",
            en: "Lesotho"
        }, {
            fr: "Lib\u00e9ria",
            en: "Liberia"
        }, {
            fr: "Libya",
            en: "Libya"
        }, {
            fr: "Liechtenstein",
            en: "Liechtenstein"
        }, {
            fr: "Lituanie",
            en: "Lithuania"
        }, {
            fr: "Luxembourg",
            en: "Luxembourg"
        }, {
            fr: "Macedonia",
            en: "Macedonia"
        }, {
            fr: "Madagascar",
            en: "Madagascar"
        }, {
            fr: "Malawi",
            en: "Malawi"
        }, {
            fr: "Malaisie",
            en: "Malaysia"
        }, {
            fr: "Maldives",
            en: "Maldives"
        }, {
            fr: "Mali",
            en: "Mali"
        }, {
            fr: "Malte",
            en: "Malta"
        }, {
            fr: "Iles Marshall",
            en: "Marshall Islands"
        }, {
            fr: "Mauritanie",
            en: "Mauritania"
        }, {
            fr: "\u00cele Maurice",
            en: "Mauritius"
        }, {
            fr: "Mexique",
            en: "Mexico"
        }, {
            fr: "Micron\u00e9sie",
            en: "Micronesia"
        }, {
            fr: "Moldova",
            en: "Moldova"
        }, {
            fr: "Mongolie",
            en: "Mongolia"
        }, {
            fr: "Maroc",
            en: "Morocco"
        }, {
            fr: "Monaco",
            en: "Monaco"
        }, {
            fr: "Montenegro",
            en: "Montenegro"
        }, {
            fr: "Mozambique",
            en: "Mozambique"
        }, {
            fr: "Namibie",
            en: "Namibia"
        }, {
            fr: "Nauru",
            en: "Nauru"
        }, {
            fr: "N\u00e9pal",
            en: "Nepal"
        }, {
            fr: "Pays-Bas",
            en: "Netherlands"
        }, {
            fr: "Nouvelle-Z\u00e9lande",
            en: "New Zealand"
        }, {
            fr: "Nicaragua",
            en: "Nicaragua"
        }, {
            fr: "Niger",
            en: "Niger"
        }, {
            fr: "Nig\u00e9ria",
            en: "Nigeria"
        }, {
            fr: "Norv\u00e8ge",
            en: "Norway"
        }, {
            fr: "Oman",
            en: "Oman"
        }, {
            fr: "Pakistan",
            en: "Pakistan"
        }, {
            fr: "Panama",
            en: "Panama"
        }, {
            fr: "Papouasie-Nouvelle-Guin\u00e9e",
            en: "Papua New Guinea"
        }, {
            fr: "Paraguay",
            en: "Paraguay"
        }, {
            fr: "P\u00e9rou",
            en: "Peru"
        }, {
            fr: "Philippines",
            en: "Philippines"
        }, {
            fr: "Pologne",
            en: "Poland"
        }, {
            fr: "Portugal",
            en: "Portugal"
        }, {
            fr: "Qatar",
            en: "Qatar"
        }, {
            fr: "Roumanie",
            en: "Romania"
        }, {
            fr: "Russia",
            en: "Russia"
        }, {
            fr: "Rwanda",
            en: "Rwanda"
        }, {
            fr: "Samoa",
            en: "Samoa"
        }, {
            fr: "Saint-Marin",
            en: "San Marino"
        }, {
            fr: "Sao Tome",
            en: "Sao Tome"
        }, {
            fr: "Arabie Saoudite",
            en: "Saudi Arabia"
        }, {
            fr: "S\u00e9n\u00e9gal",
            en: "Senegal"
        }, {
            fr: "Serbia",
            en: "Serbia"
        }, {
            fr: "Seychelles",
            en: "Seychelles"
        }, {
            fr: "Sierra Leone",
            en: "Sierra Leone"
        }, {
            fr: "Singapour",
            en: "Singapore"
        }, {
            fr: "Slovaquie",
            en: "Slovakia"
        }, {
            fr: "Slov\u00e9nie",
            en: "Slovenia"
        }, {
            fr: "Salomon, \u00celes",
            en: "Solomon Islands"
        }, {
            fr: "Somalie",
            en: "Somalia"
        }, {
            fr: "Afrique du Sud",
            en: "South Africa"
        }, {
            fr: "Espagne",
            en: "Spain"
        }, {
            fr: "Sri Lanka",
            en: "Sri Lanka"
        }, {
            fr: "Soudan",
            en: "Sudan"
        }, {
            fr: "Surinam",
            en: "Suriname"
        }, {
            fr: "Swaziland",
            en: "Swaziland"
        }, {
            fr: "Su\u00e8de",
            en: "Sweden"
        }, {
            fr: "Suisse",
            en: "Switzerland"
        }, {
            fr: "Syria",
            en: "Syria"
        }, {
            fr: "Taiwan",
            en: "Taiwan"
        }, {
            fr: "Tadjikistan",
            en: "Tajikistan"
        }, {
            fr: "Tanzania",
            en: "Tanzania"
        }, {
            fr: "Tha\u00eflande",
            en: "Thailand"
        }, {
            fr: "Togo",
            en: "Togo"
        }, {
            fr: "Tonga",
            en: "Tonga"
        }, {
            fr: "Trinidad and Tobago",
            en: "Trinidad and Tobago"
        }, {
            fr: "Tunisie",
            en: "Tunisia"
        }, {
            fr: "Turquie",
            en: "Turkey"
        }, {
            fr: "Turkm\u00e9nistan",
            en: "Turkmenistan"
        }, {
            fr: "Ouganda",
            en: "Uganda"
        }, {
            fr: "Ukraine",
            en: "Ukraine"
        }, {
            fr: "\u00c9mirats Arabes Unis",
            en: "United Arab Emirates"
        }, {
            fr: "United Kingdom",
            en: "United Kingdom"
        }, {
            fr: "United States",
            en: "United States"
        }, {
            fr: "Uruguay",
            en: "Uruguay"
        }, {
            fr: "Ouzb\u00e9kistan",
            en: "Uzbekistan"
        }, {
            fr: "Vanuatu",
            en: "Vanuatu"
        }, {
            fr: "Venezuela",
            en: "Venezuela"
        }, {
            fr: "Vietnam",
            en: "Vietnam"
        }, {
            fr: "Y\u00e9men",
            en: "Yemen"
        }, {
            fr: "Zambie",
            en: "Zambia"
        }, {
            fr: "Zimbabwe",
            en: "Zimbabwe"
        }],
        name: "Countries"
    }, {
        type: "vertical",
        values: [{
            fr: "Africa",
            en: "Africa"
        }, {
            fr: "Antarctique",
            en: "Antarctica"
        }, {
            fr: "Asia",
            en: "Asia"
        }, {
            fr: "Australie",
            en: "Australia"
        }, {
            fr: "Europe",
            en: "Europe"
        }, {
            fr: "North America",
            en: "North America"
        }, {
            fr: "South America",
            en: "South America"
        }],
        name: "Continents"
    }, {
        type: "vertical horizontal",
        values: [{
            fr: "Everyday",
            en: "Everyday"
        }, {
            fr: "Once a week",
            en: "Once a week"
        }, {
            fr: "2 to 3 times a month",
            en: "2 to 3 times a month"
        }, {
            fr: "Once a month",
            en: "Once a month"
        }, {
            fr: "Less than once a month",
            en: "Less than once a month"
        }],
        name: "How Often?"
    }, {
        type: "vertical horizontal",
        values: [{
            fr: "Jamais",
            en: "Never"
        }, {
            fr: "Rarely",
            en: "Rarely"
        }, {
            fr: "Sometimes",
            en: "Sometimes"
        }, {
            fr: "Often",
            en: "Often"
        }, {
            fr: "Always",
            en: "Always"
        }],
        name: "Frequency"
    }, {
        type: "vertical horizontal",
        values: [{
            fr: "Less than a month",
            en: "Less than a month"
        }, {
            fr: "1-6 months",
            en: "1-6 months"
        }, {
            fr: "1-3 years",
            en: "1-3 years"
        }, {
            fr: "Over 3 Years",
            en: "Over 3 Years"
        }, {
            fr: "Never used",
            en: "Never used"
        }],
        name: "How Long?"
    }, {
        type: "vertical",
        values: [{
            fr: "Very Satisfied",
            en: "Very Satisfied"
        }, {
            fr: "Satisfied",
            en: "Satisfied"
        }, {
            fr: "Neutral",
            en: "Neutral"
        }, {
            fr: "Unsatisfied",
            en: "Unsatisfied"
        }, {
            fr: "Very Unsatisfied",
            en: "Very Unsatisfied"
        }, {
            fr: "Non applicable",
            en: "N/A"
        }],
        name: "Satisfaction"
    }, {
        type: "horizontal",
        values: [{
            fr: "Very Unsatisfied",
            en: "Very Unsatisfied"
        }, {
            fr: "Unsatisfied",
            en: "Unsatisfied"
        }, {
            fr: "Neutral",
            en: "Neutral"
        }, {
            fr: "Satisfied",
            en: "Satisfied"
        }, {
            fr: "Very Satisfied",
            en: "Very Satisfied"
        }],
        name: "Satisfaction"
    }, {
        type: "vertical",
        values: [{
            fr: "Very Important",
            en: "Very Important"
        }, {
            fr: "Important",
            en: "Important"
        }, {
            fr: "Neutral",
            en: "Neutral"
        }, {
            fr: "Somewhat Important",
            en: "Somewhat Important"
        }, {
            fr: "Not at all Important",
            en: "Not at all Important"
        }, {
            fr: "Non applicable",
            en: "N/A"
        }],
        name: "Importance"
    }, {
        type: "horizontal",
        values: [{
            fr: "Not at all Important",
            en: "Not at all Important"
        }, {
            fr: "Somewhat Important",
            en: "Somewhat Important"
        }, {
            fr: "Neutral",
            en: "Neutral"
        }, {
            fr: "Important",
            en: "Important"
        }, {
            fr: "Very Important",
            en: "Very Important"
        }],
        name: "Importance"
    }, {
        type: "vertical",
        values: [{
            fr: "Very Happy",
            en: "Very Happy"
        }, {
            fr: "Happy",
            en: "Happy"
        }, {
            fr: "Indifferent",
            en: "Indifferent"
        }, {
            fr: "Unhappy",
            en: "Unhappy"
        }, {
            fr: "Very Unhappy",
            en: "Very Unhappy"
        }, {
            fr: "Non applicable",
            en: "N/A"
        }],
        name: "Happiness"
    }, {
        type: "horizontal",
        values: [{
            fr: "Very Unhappy",
            en: "Very Unhappy"
        }, {
            fr: "Unhappy",
            en: "Unhappy"
        }, {
            fr: "Indifferent",
            en: "Indifferent"
        }, {
            fr: "Happy",
            en: "Happy"
        }, {
            fr: "Very Happy",
            en: "Very Happy"
        }],
        name: "Happiness"
    }, {
        type: "vertical",
        values: [{
            fr: "Strongly Agree",
            en: "Strongly Agree"
        }, {
            fr: "Agree",
            en: "Agree"
        }, {
            fr: "Neutral",
            en: "Neutral"
        }, {
            fr: "Disagree",
            en: "Disagree"
        }, {
            fr: "Strongly Disagree",
            en: "Strongly Disagree"
        }, {
            fr: "Non applicable",
            en: "N/A"
        }],
        name: "Agreement"
    }, {
        type: "horizontal",
        values: [{
            fr: "Strongly Disagree",
            en: "Strongly Disagree"
        }, {
            fr: "Disagree",
            en: "Disagree"
        }, {
            fr: "Neutral",
            en: "Neutral"
        }, {
            fr: "Agree",
            en: "Agree"
        }, {
            fr: "Strongly Agree",
            en: "Strongly Agree"
        }],
        name: "Agreement"
    }, {
        type: "vertical",
        values: [{
            fr: "Much Better",
            en: "Much Better"
        }, {
            fr: "Somewhat Better",
            en: "Somewhat Better"
        }, {
            fr: "About the Same",
            en: "About the Same"
        }, {
            fr: "Somewhat Worse",
            en: "Somewhat Worse"
        }, {
            fr: "Much Worse",
            en: "Much Worse"
        }, {
            fr: "Don't Know",
            en: "Don't Know"
        }],
        name: "Comparison"
    }, {
        type: "horizontal",
        values: [{
            fr: "Much Worse",
            en: "Much Worse"
        }, {
            fr: "Somewhat Worse",
            en: "Somewhat Worse"
        }, {
            fr: "About the Same",
            en: "About the Same"
        }, {
            fr: "Somewhat Better",
            en: "Somewhat Better"
        }, {
            fr: "Much Better",
            en: "Much Better"
        }],
        name: "Comparison"
    }, {
        type: "vertical",
        values: [{
            fr: "Definitely",
            en: "Definitely"
        }, {
            fr: "Probably",
            en: "Probably"
        }, {
            fr: "Not Sure",
            en: "Not Sure"
        }, {
            fr: "Probably Not",
            en: "Probably Not"
        }, {
            fr: "Definitely Not",
            en: "Definitely Not"
        }],
        name: "Probability"
    }, {
        type: "horizontal",
        values: [{
            fr: "Definitely Not",
            en: "Definitely Not"
        }, {
            fr: "Probably Not",
            en: "Probably Not"
        }, {
            fr: "Not Sure",
            en: "Not Sure"
        }, {
            fr: "Probably",
            en: "Probably"
        }, {
            fr: "Definitely",
            en: "Definitely"
        }],
        name: "Probability"
    }, {
        type: "vertical horizontal",
        values: [{
            en: "1"
        }, {
            en: "2"
        }, {
            en: "3"
        }, {
            en: "4"
        }, {
            en: "5"
        }, {
            en: "6"
        }, {
            en: "7"
        }, {
            en: "8"
        }, {
            en: "9"
        }, {
            en: "10"
        }],
        name: "10 Scale"
    }, {
        type: "vertical",
        values: [{
            en: "Male"
        }, {
            en: "Female"
        }, {
            en: "Prefer Not to Answer"
        }],
        name: "Gender"
    }, {
        type: "vertical horizontal",
        values: [{
            en: "2013"
        }, {
            en: "2012"
        }, {
            en: "2011"
        }, {
            en: "2010"
        }, {
            en: "2009"
        }, {
            en: "2008"
        }, {
            en: "2007"
        }, {
            en: "2006"
        }, {
            en: "2005"
        }, {
            en: "2004"
        }, {
            en: "2003"
        }, {
            en: "2002"
        }, {
            en: "2001"
        }, {
            en: "2000"
        }, {
            en: "1999"
        }, {
            en: "1998"
        }, {
            en: "1997"
        }, {
            en: "1996"
        }, {
            en: "1995"
        }, {
            en: "1994"
        }, {
            en: "1993"
        }, {
            en: "1992"
        }, {
            en: "1991"
        }, {
            en: "1990"
        }, {
            en: "1989"
        }, {
            en: "1988"
        }, {
            en: "1987"
        }, {
            en: "1986"
        }, {
            en: "1985"
        }, {
            en: "1984"
        }, {
            en: "1983"
        }, {
            en: "1982"
        }, {
            en: "1981"
        }, {
            en: "1980"
        }, {
            en: "1979"
        }, {
            en: "1978"
        }, {
            en: "1977"
        }, {
            en: "1976"
        }, {
            en: "1975"
        }, {
            en: "1974"
        }, {
            en: "1973"
        }, {
            en: "1972"
        }, {
            en: "1971"
        }, {
            en: "1970"
        }, {
            en: "1969"
        }, {
            en: "1968"
        }, {
            en: "1967"
        }, {
            en: "1966"
        }, {
            en: "1965"
        }, {
            en: "1964"
        }, {
            en: "1963"
        }, {
            en: "1962"
        }, {
            en: "1961"
        }, {
            en: "1960"
        }, {
            en: "1959"
        }, {
            en: "1958"
        }, {
            en: "1957"
        }, {
            en: "1956"
        }, {
            en: "1955"
        }, {
            en: "1954"
        }, {
            en: "1953"
        }, {
            en: "1952"
        }, {
            en: "1951"
        }, {
            en: "1950"
        }, {
            en: "1949"
        }, {
            en: "1948"
        }, {
            en: "1947"
        }, {
            en: "1946"
        }, {
            en: "1945"
        }, {
            en: "1944"
        }, {
            en: "1943"
        }, {
            en: "1942"
        }, {
            en: "1941"
        }, {
            en: "1940"
        }, {
            en: "1939"
        }, {
            en: "1938"
        }, {
            en: "1937"
        }, {
            en: "1936"
        }, {
            en: "1935"
        }, {
            en: "1934"
        }, {
            en: "1933"
        }, {
            en: "1932"
        }, {
            en: "1931"
        }, {
            en: "1930"
        }, {
            en: "1929"
        }, {
            en: "1928"
        }, {
            en: "1927"
        }, {
            en: "1926"
        }, {
            en: "1925"
        }, {
            en: "1924"
        }, {
            en: "1923"
        }, {
            en: "1922"
        }, {
            en: "1921"
        }, {
            en: "1920"
        }, {
            en: "1919"
        }, {
            en: "1918"
        }, {
            en: "1917"
        }, {
            en: "1916"
        }, {
            en: "1915"
        }, {
            en: "1914"
        }, {
            en: "1913"
        }, {
            en: "1912"
        }, {
            en: "1911"
        }, {
            en: "1910"
        }, {
            en: "1909"
        }, {
            en: "1908"
        }, {
            en: "1907"
        }, {
            en: "1906"
        }, {
            en: "1905"
        }, {
            en: "1904"
        }, {
            en: "1903"
        }, {
            en: "1902"
        }, {
            en: "1901"
        }, {
            en: "1900"
        }],
        name: "Years"
    }];
$(document).ready(function() {
    sf_createQuestions();
    $.fn.selectpicker && $(".selectpicker").selectpicker();
    $("#CKeditor").text("");
    CKEDITOR.replace("CKeditor");
    $("#sf_date_started").datepicker({
        showOn: "button",
        buttonText: COM_SURVEYFORCE_SELECT_DATE,
        showOptions: {
            direction: "up"
        }
    });
    $("#sf_date_expired").datepicker({
        showOn: "button",
        buttonText: COM_SURVEYFORCE_SELECT_DATE,
        showOptions: {
            direction: "up"
        }
    });
    $("li.tool, a.tool").tooltip({
        hide: {
            effect: "explode",
            delay: 250
        }
    });
    $("#basicquestions li").draggable({
        appendTo: "body",
        helper: "clone",
        start: function(a, b) {
            dropped = !1;
            b.helper.addClass("tools-move")
        }
    });
    $("#survey-questions" + currPage).droppable({
        drop: function(a, b) {
            qtype = $(b.helper).attr("field-type");
            sfAddQuestion(qtype)
        }
    }).sortable({
        axis: "y",
        placeholder: "ui-state-highlight",
        cursor: "move",
        stop: function(a, b) {
            sf_SortQuestions(a, b)
        }
    });
    $(".viewport .tabs").sortable({
        placeholder: "ui-state-highlight",
        cursor: "move",
        stop: function(a, b) {
            sfOrderingPages(a, b);
            sf_SortQuestions(a, b)
        }
    })
});
var sfPublishSurvey = function(a, b) {
        window.location.href = b + "index.php?option=com_surveyforce&view=survey&id=" + a;
        return !1
    },
    sf_SortQuestions = function(a, b) {
        var c = 0,
            d = [],
            e = $(".page");
        $(e).each(function() {
            var a = $(this).find("li.field");
            $(a).each(function() {
                var a = $(this).attr("id");
                questionsStack[a] && (questionsStack[a].questOrdering = c);
                c++
            });
            for (var b in questionsStack)
                if ("page-break" == questionsStack[b].sf_qtype && !sf_inArray(d, b)) {
                    questionsStack[b].questOrdering = c;
                    c++;
                    d.push(b);
                    break
                }
        })
    },
    sf_inArray = function(a, b) {
        if (!a.length) return !1;
        for (var c = 0; c < a.length; c++)
            if (a[c] == b) return !0;
        return !1
    },
    sf_createQuestions = function() {
        if (questionsStack) {
            $(".placeholder").remove();
            for (var a in questionsStack) {
                var b = '<li class="field" name="' + questionsStack[a].sf_qtype + '" style="" id="' + a + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">' + questionsStack[a].sf_qtitle + '</h3><div class="description"></div>{ANSWERS}</li>';
                switch (questionsStack[a].sf_qtype) {
                    case "section-separator":
                        var c = questionsStack[a].sections;
                        if (c.length)
                            for (var d = 0; d < c.length; d++) {
                                qid = c[d];
                                for (var e in questionsStack) questionsStack[e].id == qid && (questionsStack[a].sections[d] = e)
                            }
                        b = b.replace("{ANSWERS}", "");
                        break;
                    case "pick-one":
                    case "pick-many":
                        var f = "pick-one" == questionsStack[a].sf_qtype ? "radio" : "checkbox",
                            c = sfGenerateID();
                        if (questionsStack[a].answers.length) {
                            g = '<ul class="choices" data-id="' + c + '">';
                            for (d = 0; d < questionsStack[a].answers.length; d++) g += '<li class="choice"><label><label class="clean-input-wrap"><input type="' + f + '" name="' + c + '"><span class="clean-input"></span></label><span class="choice-value">' + questionsStack[a].answers[d].title + "</span></label></li>";
                            questionsStack[a].answers[0].other_option && (g += '<li class="choice other"><label><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label> <span class="choice-value">' + questionsStack[a].answers[0].other_option_text + '</span><input type="text" "="" class="text dummy"></label></li>');
                            g += "</ul>";
                            b = b.replace("{ANSWERS}", g)
                        }
                        break;
                    case "short-answer":
                        b = sfReplaceShortAnswer(b);
                        b = b.replace("{ANSWERS}", "");
                        break;
                    case "ranking":
                    case "ranking-dragdrop":
                        c = sfGenerateID();
                        if (questionsStack[a].answers.length) {
                            if ("ranking" == questionsStack[a].sf_qtype) {
                                for (var g = '<ul class="choices" data-id="' + c + '">', f = "<select>", d = 0; d < questionsStack[a].answers.length; d++) f += '<option value="' + questionsStack[a].answers[d].right + '">' + questionsStack[a].answers[d].right + "</option>";
                                f += "</select>";
                                for (d = 0; d < questionsStack[a].answers.length; d++) g += '<li class="ranking-left">' + questionsStack[a].answers[d].left + '</li><li class="ranking-right">' + f + '</li><li class="ranking-break">';
                                g += "</ul>"
                            }
                            if ("ranking-dragdrop" == questionsStack[a].sf_qtype) {
                                g = '<ul class="choices" data-id="' + c + '">';
                                for (d = 0; d < questionsStack[a].answers.length; d++) g += '<li class="ranking-left fixed">' + questionsStack[a].answers[d].left + '</li><li class="ranking-right ui-widget-header dragable">' + questionsStack[a].answers[d].right + '</li><li class="ranking-break">';
                                g += "</ul>"
                            }
                            b = b.replace("{ANSWERS}", g)
                        }
                        break;
                    case "boilerplate":
                        b = b.replace("{ANSWERS}", "");
                        break;
                    case "ranking-dropdown":
                    case "likert-scale":
                        c = sfGenerateID();
                        if ("ranking-dropdown" == questionsStack[a].sf_qtype) {
                            g = '<ul class="choices" data-id="' + c + '">';
                            f = "<select>";
                            if (questionsStack[a].answers.ranks.length)
                                for (d = 0; d < questionsStack[a].answers.ranks.length; d++) f += '<option value="' + questionsStack[a].answers.ranks[d] + '">' + questionsStack[a].answers.ranks[d] + "</option>";
                            f += "</select>";
                            if (questionsStack[a].answers.options.length)
                                for (d = 0; d < questionsStack[a].answers.options.length; d++) g += '<li class="ranking-left">' + questionsStack[a].answers.options[d] + '</li><li class="ranking-right">' + f + '</li><li class="ranking-break">';
                            g += "</ul>"
                        }
                        if ("likert-scale" == questionsStack[a].sf_qtype) {
                            g = '<table class="likert-scale">';
                            c = "<thead><tr><th></th>";
                            if (questionsStack[a].answers.scales.length)
                                for (d = 0; d < questionsStack[a].answers.scales.length; d++) c += "<th>" + questionsStack[a].answers.scales[d] + "</th>";
                            c += "</tr></thead>";
                            g += c;
                            f = "<tbody>";
                            if (questionsStack[a].answers.options.length)
                                for (d = 0; d < questionsStack[a].answers.options.length; d++) {
                                    c = sfGenerateID();
                                    f += '<tr nameid="' + c + '">';
                                    f += "<td>" + questionsStack[a].answers.options[d] + "</td>";
                                    if (questionsStack[a].answers.scales.length)
                                        for (var h = 0; h < questionsStack[a].answers.scales.length; h++) f += '<td><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label></td>';
                                    f += "</tr>"
                                }
                            f += "</tbody></table>";
                            g += f
                        }
                        b = b.replace("{ANSWERS}", g);
                        break;
                    case "page-break":
                        sfAddPage(!1, a);
                        $(".placeholder").remove();
                        continue
                }
                questionsStack[a].page = currPage;
                $("#survey-questions" + currPage).append(b);
                sfSelectQuestion(a)
            }
            for (a in questionsStack) {
                if (questionsStack[a].hides.length)
                    for (b = 0; b < questionsStack[a].hides.length; b++) {
                        e = questionsStack[a].hides[b].question;
                        var c = questionsStack[a].hides[b].answer,
                            k;
                        for (k in questionsStack)
                            if (questionsStack[k].id == e) {
                                questionsStack[a].hides[b].question = k;
                                switch (questionsStack[k].sf_qtype) {
                                    case "pick-one":
                                    case "pick-many":
                                        if (questionsStack[k].answers.length)
                                            for (d = 0; d < questionsStack[k].answers.length; d++)
                                                if (questionsStack[k].answers[d].id == c) {
                                                    questionsStack[a].hides[b].answer = d + 1;
                                                    break
                                                }
                                        break;
                                    case "ranking-dragdrop":
                                    case "ranking":
                                        if (questionsStack[k].answers.length)
                                            for (d = 0; d < questionsStack[k].answers.length; d++)
                                                if (questionsStack[k].answers[d].leftid == c) {
                                                    questionsStack[a].hides[b].answer = d + 1;
                                                    break
                                                }
                                        break;
                                    case "ranking-dropdown":
                                    case "likert-scale":
                                        if (questionsStack[k].answers.oid.length)
                                            for (d = 0; d < questionsStack[k].answers.oid.length; d++)
                                                if (questionsStack[k].answers.oid[d] == c) {
                                                    questionsStack[a].hides[b].answer = d + 1;
                                                    break
                                                }
                                }
                                break
                            }
                    }
                if (questionsStack[a].rules.length)
                    for (b = 0; b < questionsStack[a].rules.length; b++) {
                        e = questionsStack[a].rules[b].question;
                        c = questionsStack[a].rules[b].answer;
                        g = questionsStack[a].rules[b].option;
                        switch (questionsStack[a].sf_qtype) {
                            case "pick-one":
                            case "pick-many":
                                if (questionsStack[a].answers.length)
                                    for (d = 0; d < questionsStack[a].answers.length; d++)
                                        if (questionsStack[a].answers[d].id == c) {
                                            questionsStack[a].rules[b].answer = d + 1;
                                            break
                                        }
                                break;
                            case "ranking-dragdrop":
                            case "ranking":
                                if (questionsStack[a].answers.length) {
                                    for (d = 0; d < questionsStack[a].answers.length; d++)
                                        if (questionsStack[a].answers[d].rightid == c) {
                                            questionsStack[a].rules[b].answer = d + 1;
                                            break
                                        }
                                    for (d = 0; d < questionsStack[a].answers.length; d++)
                                        if (questionsStack[a].answers[d].leftid == g) {
                                            questionsStack[a].rules[b].option = d + 1;
                                            break
                                        }
                                }
                                break;
                            case "ranking-dropdown":
                            case "likert-scale":
                                if (questionsStack[a].answers.oid.length) {
                                    for (d = 0; d < questionsStack[a].answers.oid.length; d++)
                                        if (questionsStack[a].answers.oid[d] == g) {
                                            questionsStack[a].rules[b].option = d + 1;
                                            break
                                        }
                                    for (d = 0; d < questionsStack[a].answers.oid.length; d++)
                                        if ("ranking-dropdown" == questionsStack[a].sf_qtype) {
                                            if (questionsStack[a].answers.rid[d] == c) {
                                                questionsStack[a].rules[b].answer = d + 1;
                                                break
                                            }
                                        } else if (questionsStack[a].answers.sid[d] == c) {
                                        questionsStack[a].rules[b].answer = d + 1;
                                        break
                                    }
                                }
                        }
                        for (k in questionsStack)
                            if (questionsStack[k].id == e) {
                                questionsStack[a].rules[b].question = k;
                                break
                            }
                    }
            }
        }
        sfSelectPage(1)
    },
    sfSelectQuestion = function(a) {
        $("#" + a).bind("click", function() {
            sfClearActives();
            $(this).addClass("active");
            $(this).find("i.remove").css("opacity", "1");
            currQuestion = $(this);
            sfGetOptions(a);
            return !0
        })
    },
    sfAddPage = function(a, b) {
        a && (b = sfGenerateID(), questionsStack[b] = {}, questionsStack[b].exists = 1, questionsStack[b].published = 1, questionsStack[b].sf_qtitle = "Page Break", questionsStack[b].sf_qtype = "page-break", questionsStack[b].is_final_question = 0, questionsStack[b].sf_compulsory = 0, questionsStack[b].sf_default_hided = 0, questionsStack[b].questOrdering = questOrdering, questOrdering++);
        var c = lastPage;
        lastPage += 1;
        var d = $("#page" + c + " div.title").html();
        $("#page" + currPage).hide();
        $('<div class="pages" id="page' + lastPage + '" questid="' + b + '"><div class="title">' + d + '</div><ol id="survey-questions' + lastPage + '" class="page active"><li class="placeholder" style="display: list-item;">' + COM_SF_YOU_HAVANT_ADDED_WITH_SLASH + "</li></ol></div>").insertAfter(".viewport #page" + c);
        $("#page" + lastPage).show();
        $("#tab" + currPage).removeClass("active");
        c = '<a style="" name="0" class="button button-tab-bottom page-button active" href="#" id="tab' + lastPage + '" onclick="javascript:sfSelectPage(' + lastPage + ');">' + lastPage + "</a>";
        $(".viewport .tabs").append(c);
        $("#survey-questions" + lastPage).droppable({
            drop: function(a, b) {
                qtype = $(b.helper).attr("field-type");
                sfAddQuestion(qtype)
            }
        }).sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            stop: function(a, b) {
                sf_SortQuestions(a, b)
            }
        });
        currQuestion = null;
        $("ol.page li.field").removeClass("active");
        $("ol.page li.field i.remove").css("opacity", "0.2");
        currPage = lastPage;
        sfRemoveChoicesPanel();
        sfDisableOptions();
        sf_SortQuestions();
        sfGoToAddQuestion();
        return !0
    },
    sfSelectPage = function(a) {
        $("#page" + currPage).hide();
        $("#page" + a).show();
        $(".viewport .tabs #tab" + currPage).removeClass("active");
        $(".viewport .tabs #tab" + a).addClass("active");
        $("#survey-questions" + a).droppable({
            drop: function(a, c) {
                qtype = $(c.helper).attr("field-type");
                sfAddQuestion(qtype)
            }
        }).sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            stop: function(a, c) {
                sf_SortQuestions(a, c)
            }
        });
        currPage = a;
        currQuestion = null;
        $("ol.page li.field").removeClass("active");
        $("ol.page li.field i.remove").css("opacity", "0.2");
        sfRemoveChoicesPanel();
        sfDisableOptions();
        sfGoToAddQuestion();
        return !0
    },
    sfDeletePage = function() {
        1 < lastPage && $("#dialog-pageremove-confirm").dialog({
            autoOpen: !0,
            height: 300,
            width: 350,
            modal: !0,
            buttons: {
                Yes: function() {
                    sfRemovePage();
                    $(this).dialog("close")
                },
                Cancel: function() {
                    $(this).dialog("close")
                }
            }
        })
    },
    sfRemovePage = function() {
        var a = [],
            b;
        for (b in questionsStack) questionsStack[b].page == currPage && a.push(b);
        if (a.length)
            for (b = 0; b < a.length; b++) delete questionsStack[a[b]];
        a = $("#page" + currPage).attr("questid");
        delete questionsStack[a];
        $("#page" + currPage).remove();
        $(".viewport .tabs #tab" + currPage).remove();
        a = sfReorderPages();
        $("#page" + a).show();
        $(".viewport .tabs #tab" + a).addClass("active");
        currPage = a;
        currQuestion = null;
        $("ol.page li.field").removeClass("active");
        $("ol.page li.field i.remove").css("opacity", "0.2");
        sfRemoveChoicesPanel();
        sfDisableOptions();
        sfGoToAddQuestion();
        return !0
    },
    sfReorderPages = function() {
        var a = currPage - 1;
        a || (a = currPage);
        for (var b = currPage + 1; b <= lastPage; b++)
            for (question in questionsStack) questionsStack[question].page == b && (questionsStack[question].page = b - 1);
        for (b = currPage + 1; b <= lastPage; b++)
            if ($("#page" + b)) {
                var c = b - 1;
                $("#page" + b).attr("id", "page" + c);
                $("#survey-questions" + b).attr("id", "survey-questions" + c);
                $(".viewport .tabs #tab" + b).text(c);
                $(".viewport .tabs #tab" + b).attr("onclick", "javascript:sfSelectPage(" + c + ");");
                $(".viewport .tabs #tab" + b).attr("id", "tab" + c)
            }--lastPage;
        return a
    },
    sfOrderingPages = function(a, b) {
        var c = parseInt($(b.item).text()),
            d = parseInt($(b.item).prev().text()),
            e = parseInt($(b.item).next().text()),
            f = $(".viewport .tabs a");
        if (d || e) d ? $("#page" + c).insertAfter("#page" + d) : e && $("#page" + c).insertBefore("#page" + e);
        var g = $(".viewport .pages"),
            h = 1;
        $(g).each(function(a) {
            var b = $(g[a]).find(".page li").not(".placeholder");
            b.length && $(b).each(function(a) {
                a = $(b[a]).attr("id");
                questionsStack[a] && (questionsStack[a].page = h)
            });
            h++
        });
        h = 1;
        $(g).each(function(a) {
            $(g[a]).attr("id", "page" + h);
            $(g[a]).find(".page").attr("id", "survey-questions" + h);
            $(f[a]).text(h);
            $(f[a]).attr("onclick", "javascript:sfSelectPage(" + h + ");");
            $(f[a]).attr("id", "tab" + h);
            h++
        });
        currPage = parseInt($(".viewport .tabs a.active").text());
        return !0
    },
    sfOpenCKEEditor = function(a, b) {
        var c = $(a).parent().prev().val();
        CKEDITOR.instances.CKeditor.destroy();
        $("#CKeditor").val(c);
        CKEDITOR.replace("CKeditor");
        $("#dialog-editor").dialog({
            autoOpen: !0,
            width: 700,
            modal: !0,
            buttons: {
                Ok: function() {
                    var d = CKEDITOR.instances.CKeditor.getData();
                    $(a).parent().prev().val(d);
                    $("#CKeditor").val(c);
                    var e = $(currQuestion).attr("id");
                    switch (b) {
                        case "questionTitle":
                            questionsStack[e].sf_qtitle = d;
                            "short-answer" == questionsStack[e].sf_qtype && (d = sfReplaceShortAnswer(d));
                            $(currQuestion).find("h3.title").html(d);
                            break;
                        case "questionDescr":
                            $(currQuestion).find("div.description").html(d);
                            questionsStack[e].sf_qdescription = d;
                            break;
                        case "surveyDescr":
                            $(".viewport div.title p.description").html(d)
                    }
                    $(this).dialog("close")
                },
                Cancel: function() {
                    $(this).dialog("close")
                }
            }
        });
        return !0
    },
    sfClone = function(a) {
        var b = {},
            c;
        for (c in a)
            if (a[c] instanceof Array) {
                var d = [];
                if (a[c].length)
                    for (var e = 0; e < a[c].length; e++) {
                        d[e] = {};
                        for (var f in a[c][e]) d[e][f] = a[c][e][f]
                    }
                b[c] = d
            } else b[c] = a[c];
        return b
    },
    sfMoveQuestionToAction = function(a) {
        $("#page" + a + " .placeholder").remove();
        var b = $(currQuestion).attr("id");
        questionsStack[b].page = a;
        $("#page" + a + " #survey-questions" + a).append(currQuestion);
        $("#page" + a).css("display", "block");
        $("#page" + currPage).css("display", "none");
        $(".viewport .tabs a").removeClass("active");
        $(".viewport .tabs #tab" + a).addClass("active");
        currPage = a;
        sf_SortQuestions();
        return !0
    },
    sfMoveQuestionTo = function() {
        $("#sf_move_to").html("");
        $("#sf_move_to").html("<option value=''>" + COM_SURVEYFORCE_SELECT_PAGE + "</option>");
        for (var a = 1; a <= lastPage; a++)
            if (a != currPage) {
                var b = "<option value='" + a + "'>" + a + "</oprion>";
                $("#sf_move_to").append(b)
            }
        $("#dialog-move-to").dialog({
            autoOpen: !0,
            height: 300,
            width: 350,
            modal: !0,
            buttons: {
                Move: function() {
                    var a = $("#sf_move_to").val();
                    if ("" == a) return alert(COM_SURVEYFORCE_SELECT_PAGE), !0;
                    sfMoveQuestionToAction(a);
                    $(this).dialog("close")
                },
                Cancel: function() {
                    $(this).dialog("close")
                }
            }
        });
        return !0
    },
    sfDublicateQuestion = function() {
        var a = $(currQuestion).attr("id"),
            b = sfGenerateID(),
            c = $(currQuestion).clone();
        $(c).attr("id", b);
        $("#survey-questions" + currPage).append(c);
        questionsStack[b] = sfClone(questionsStack[a]);
        sfClearActives();
        $("#" + b).addClass("active");
        $("#" + b + " i.remove").css("opacity", "1");
        $("#survey-questions" + currPage).selectable();
        $("#" + b).click(function() {
            sfClearActives();
            $(this).addClass("active");
            $(this).find("i.remove").css("opacity", "1");
            currQuestion = $(this);
            sfGetOptions(b);
            return !0
        });
        "pick-one" == $("#" + b).attr("name") && (a = sfGenerateID(), $("#" + b).find(".choices").attr("data-id", a), $("#" + b).find(".choices li.choice input").attr("name", a));
        currQuestion = c;
        sf_SortQuestions();
        return !0
    },
    sfNextStep = function() {
        if (!$("#sf_name").val()) return notif({
            type: "info",
            msg: "Fill survey name please.",
            position: "right",
            fade: !0,
            timeout: 3E3
        }), $("#sf_name").focus(), !1;
        sf_step++;
        $("#sf_step").val(sf_step);
        sfSaveSurvey(!1);
        2 == sf_step && ($("#surveyButton").removeClass("active"), $("#survey").removeClass("active"), $("#questionsButton").removeClass("disabled"), $("#questionsButton").addClass("active"), $("#questions").addClass("active"), $("#questions").removeAttr("style"), $("#survey").find(".nextButton").remove(), $("#questions").append('<div class="nextButton"><button class="btn btn-primary" onclick="sfNextStep();return false;">Next step</button></div>'), $("#questionsButton a").click(function(a) {
            a.preventDefault();
            $(this).tab("show")
        }), $(".done").css("left", "7px"), $(".done").css("width", "148px"), $(".not-done").css("left", "154px"), $(".not-done").css("width", "300px"));
        3 == sf_step && ($("#questionsButton").removeClass("active"), $("#questions").removeClass("active"), $("#pageButton").removeClass("disabled"), $("#pageButton").addClass("active"), $("#page").addClass("active"), $("#page").removeAttr("style"), $("#questions").find(".nextButton").remove(), $("#pageButton a").click(function(a) {
            a.preventDefault();
            $(this).tab("show")
        }), $(".done").css("left", "7px"), $(".done").css("width", "455px"), $(".not-done").hide());
        return !0
    },
    sfGoToAddQuestion = function() {
        if (2 > sf_step) return notif({
            type: "info",
            msg: "Click on 'Next step' to add question",
            position: "right",
            fade: !0,
            timeout: 3E3
        }), !1;
        $(".tab-pane").removeClass("active");
        $(".nav-tabs li").removeClass("active");
        $("#questionsButton").addClass("active");
        $("#questions").addClass("active");
        $("#basic").addClass("in");
        $("#collapseProperties").removeClass("in");
        $("#collapseChoices").removeClass("in");
        sfDisableOptions();
        return !0
    },
    sfChangeQuestionTitle = function() {
        var a = $("textarea[name='sf_qtitle']").val(),
            b = $(currQuestion).attr("id");
        questionsStack[b].sf_qtitle = a;
        "short-answer" == questionsStack[b].sf_qtype && (a = sfReplaceShortAnswer(a));
        $("li#" + b).find("h3.title").html(a);
        refreshQuestionsList();
        changeQuestionInHides();
        return !0
    },
    sfChangeQuestionDescription = function() {
        var a = $("textarea[name='sf_qdescription']").val(),
            b = $(currQuestion).attr("id");
        $("li#" + b).find("div.description").html(a);
        questionsStack[b].sf_qdescription = a;
        return !0
    },
    sfSelectCheckbox = function(a) {
        var b = $(a).attr("name"),
            c = $(currQuestion).attr("id");
        if ($(a).prop("checked")) var d = 1;
        else $(a).prop("checked") || (d = 0);
        questionsStack[c][b] = d;
        return !0
    },
    sfRemoveImage = function() {
        $("#image_file").val("");
        $("#sf_image").val("");
        $("#bkg_thumb span").remove();
        $(".pages").css("background", "white");
        return !0
    },
    sfSelectFile = function(a) {
        $(a).next().click();
        file = a;
        fileInterval = setInterval("sfCheckSelectFile()", 300);
        return !0
    },
    sfCheckSelectFile = function() {
        if ("" != $(file).next().val()) {
            var a = $(file).next().val();
            clearInterval(fileInterval);
            $(file).prev().val(a);
            window.File && window.FileReader && window.FileList && window.Blob ? document.getElementById("image_file").addEventListener("change", handleFileSelect, !1) : alert(COM_SURVEYFORCE_FILE_API_ARE_NOT_SUPPORTED)
        }
        return !0
    },
    handleFileSelect = function(a) {
        a = a.target.files;
        for (var b = 0, c; c = a[b]; b++)
            if (c.type.match("image.*")) {
                var d = new FileReader;
                d.onload = function(a) {
                    return function(b) {
                        $("#bkg_thumb span").remove();
                        var c = document.createElement("span");
                        c.innerHTML = ['<img class="thumb" src="', b.target.result, '" title="', escape(a.name), '"/>'].join("");
                        $("#bkg_thumb").append(c);
                        $(".pages").css("background", "url(" + b.target.result + ")")
                    }
                }(c);
                d.readAsDataURL(c)
            }
    },
    sfOpenEditButton = function(a) {
        $(a).next().slideDown();
        return !0
    },
    sfCloseEditButton = function(a) {
        $(a).next().slideUp();
        return !0
    },
    sfChangeSurveyName = function() {
        var a = $("#sf_name").val();
        $("h2.title").text(a);
        return !0
    },
    sfChangeSurveyDescr = function() {
        var a = $("#sf_descr").val();
        $("p.description").html(a);
        return !0
    },
    sfDeleteQuestion = function(a) {
        var b = a ? $(a).parent() : currQuestion;
        a = $(b).attr("id");
        $(b).fadeOut(400, function() {
            $(b).remove();
            $("#fieldProperties").css("display", "none");
            $("#Settings").css("display", "none");
            $("#fieldPropertiesDisable").css("display", "block");
            $("#SettingsDisable").css("display", "block");
            $("#panelActions").css("display", "none");
            sfRemoveChoicesPanel()
        });
        delete questionsStack[a];
        currQuestion = null;
        refreshQuestionsList();
        refreshAnswersList();
        sf_SortQuestions();
        return !0
    },
    sfRemoveQuestion = function(a) {
        $("#dialog-confirm").dialog({
            autoOpen: !0,
            height: 300,
            width: 350,
            modal: !0,
            buttons: {
                Yes: function() {
                    sfDeleteQuestion(a);
                    $(this).dialog("close")
                },
                Cancel: function() {
                    $(this).dialog("close")
                }
            }
        });
        return !0
    },
    sfRefreshBulkList = function(a) {
        var b = "";
        a = $(a).val();
        if (BULKS.length)
            for (var c = 0; c < BULKS.length; c++)
                if (bulkObject = BULKS[c], a == bulkObject.name) {
                    a = bulkObject.values;
                    for (c = 0; c < a.length; c++) b += bulkObject.values[c].en + "\n";
                    $("#bulk_list").val(b);
                    break
                }
        return !0
    },
    sfAddBulkList = function(a, b) {
        var c = $(currQuestion).attr("id");
        questionsStack[c].answers[0].other_option = 0;
        questionsStack[c].answers[0].other_option_text = "";
        delete questionsStack[c].answers;
        questionsStack[c].answers = [];
        questionsStack[c].choiceStyle ? $("#" + c + " .choices option").remove() : $("#" + c + " .choices li").remove();
        $("#choices-list li").remove();
        if (BULKS.length)
            for (c = 0; c < BULKS.length; c++)
                if (bulkObject = BULKS[c], a == bulkObject.name) {
                    for (var c = bulkObject.values, d = 0; d < c.length; d++) sfAddChoice(bulkObject.values[d].en, !0, b);
                    break
                }
        $("#other_option_cb").prop("checked", !1);
        $("#other_option").val("");
        return !0
    },
    sfDialogBulkList = function(a) {
        $("#dialog-bulk").dialog({
            autoOpen: !0,
            height: 400,
            width: 550,
            modal: !0,
            buttons: {
                Add: function() {
                    var b = $("#bulk-selector").val();
                    sfAddBulkList(b, a);
                    $(this).dialog("close")
                },
                Cancel: function() {
                    $(this).dialog("close")
                }
            }
        });
        return !0
    },
    sfAddQuestion = function(a) {
        var b = sfGenerateID();
        switch (a) {
            case "section-separator":
                var c = '<li class="field" name="section-separator" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Section Heading</h3><div class="description"></div></li>';
                break;
            case "pick-one":
                c = sfGenerateID();
                c = '<li class="field" name="pick-one" style="" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + c + '"><li class="choice"><label><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label> <span class="choice-value">Choice 1</span></label></li><li class="choice"><label><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label> <span class="choice-value">Choice 2</span></label></li></ul></li>';
                break;
            case "pick-many":
                c = sfGenerateID();
                c = '<li class="field" name="pick-many" style="" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + c + '"><li class="choice"><label><label class="clean-input-wrap"><input type="checkbox" name="' + c + '"><span class="clean-input"></span></label> <span class="choice-value">Choice 1</span></label></li><li class="choice"><label><label class="clean-input-wrap"><input type="checkbox" name="' + c + '"><span class="clean-input"></span></label><span class="choice-value">Choice 2</span></label></li></ul></li>';
                break;
            case "ranking":
                c = sfGenerateID();
                c = '<li class="field" name="ranking" style="" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + c + '"><li class="ranking-left">Option 1</li><li class="ranking-right"><select><option value="Rank 1">Rank 1</option><option value="Rank 2">Rank 2</option></select></li><li class="ranking-break"></li><li class="ranking-left">Option 2</li><li class="ranking-right"><select><option value="Rank 1">Rank 1</option><option value="Rank 2">Rank 2</option></select></li><li class="ranking-break"></li></ul></li>';
                break;
            case "short-answer":
                c = sfReplaceShortAnswer('<li class="field" name="short-answer" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Every {x} in question text will be replaced by input box. If the number of {x} is more than zero no large text area will be displayed. To place text area with input box in question text use {y} tag.</h3><div class="description"></div></li>');
                break;
            case "ranking-dragdrop":
                c = sfGenerateID();
                c = '<li class="field" name="ranking-dragdrop" style="" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + c + '"><li class="ranking-left fixed">Option 1</li><li class="ranking-right ui-widget-header dragable">Rank 1</li><li class="ranking-break"></li><li class="ranking-left fixed">Option 2</li><li class="ranking-right ui-widget-header dragable">Rank 2</li><li class="ranking-break"></li></ul></li>';
                break;
            case "boilerplate":
                c = '<li class="field" name="boilerplate" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Boilerplate</h3><div class="description"></div></li>';
                break;
            case "page-break":
                return sfAddPage(!0), !0;
            case "ranking-dropdown":
                c = sfGenerateID();
                c = '<li class="field" name="ranking-dropdown" style="" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + c + '"><li class="ranking-left">Option 1</li><li class="ranking-right"><select><option value="1">1</option><option value="2">2</option></select></li><li class="ranking-break"></li><li class="ranking-left">Option 2</li><li class="ranking-right"><select><option value="1">1</option><option value="2">2</option></select></li><li class="ranking-break"></li></ul></li>';
                break;
            case "likert-scale":
                var c = sfGenerateID(),
                    d = sfGenerateID(),
                    c = '<li class="field" name="likert-scale" style="" id="' + b + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><table class="likert-scale"><thead><tr><th></th><th>Scale 1</th><th>Scale 2</th><th>Scale 3</th><th>Scale 4</th></tr></thead><tbody><tr nameid="' + c + '"><td>Option 1</td><td><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label></td></tr><tr nameid="' + d + '"><td>Option 2</td><td><label class="clean-input-wrap"><input type="radio" name="' + d + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + d + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + d + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + d + '"><span class="clean-input"></span></label></td></tr></tbody></table></li>'
        }
        a && sfInsertQuestion(c, b);
        return !0
    },
    sfReplaceShortAnswer = function(a) {
        a = a.replace(/{x}/g, '<input type="text" class="text" style="width: 200px;" value="">');
        return a = a.replace(/{y}/g, '<textarea class="text" style="width: 400px;height:200px;"></textarea>')
    },
    sfEnableOptions = function() {
        currQuestion ? ($("#fieldPropertiesDisable").hide(), $("#fieldProperties").show(), $("#SettingsDisable").hide(), $("#Settings").show(), currQuestion.attr('name') != 'short-answer' ? $("#panelRules").show() : '', $("#panelActions").css("display", "block")) : sfDisableOptions()
    },
    sfDisableOptions = function() {
        $("#fieldPropertiesDisable").show();
        $("#fieldProperties").hide();
        $("#SettingsDisable").show();
        $("#Settings").hide();
        $("#panelRules").hide();
        $("#panelActions").css("display", "none")
    },
    sfSetSelected = function(a, b) {
        for (var c = $("select[name='" + a + "']").find("option"), d = 0; d < c.length; d++) {
            var e = c[d];
            $(e).val() == parseInt(b) ? ($(e).attr("selected", "selected"), $(e).prop("selected", !0)) : ($(e).removeAttr("selected"), $(e).prop("selected", !1))
        }
        return !0
    },
    sfSetCheckbox = function(a, b) {
        b ? ($("input[name='" + a + "']").attr("checked", "checked"), $("input[name='" + a + "']").prop("checked", !0)) : b || ($("input[name='" + a + "']").attr("checked", ""), $("input[name='" + a + "']").prop("checked", !1));
        return !0
    },
    sfSetIscale = function(a) {
        var b = $(currQuestion).attr("id");
        questionsStack[b].sf_iscale = a
    },
    sfSetOptionsFields = function(a) {
        var b = questionsStack[a].sf_qtitle ? questionsStack[a].sf_qtitle : "Enter Question Text",
            c = questionsStack[a].sf_qdescription ? questionsStack[a].sf_qdescription : "",
            d = questionsStack[a].sf_iscale ? questionsStack[a].sf_iscale : "",
            e = "undefined" != typeof questionsStack[a].published ? questionsStack[a].published : 1,
            f = "undefined" != typeof questionsStack[a].sf_compulsory ? questionsStack[a].sf_compulsory : 0,
            g = "undefined" != typeof questionsStack[a].sf_default_hided ? questionsStack[a].sf_default_hided : 0,
            h = "undefined" != typeof questionsStack[a].is_final_question ? questionsStack[a].is_final_question : 0;
        sfSetSelected("sf_qtype", questionsStack[a].sf_qtype ? questionsStack[a].sf_qtype : "section-separator");
        $("textarea[name='sf_qtitle']").val(b);
        $("textarea[name='sf_qdescription']").val(c);
        sfSetSelected("sf_iscale", d);
        sfSetCheckbox("published", e);
        sfSetCheckbox("sf_compulsory", f);
        sfSetCheckbox("sf_default_hided", g);
        sfSetCheckbox("is_final_question", h)
    },
    sfChangeRightOption = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent(),
            d = $(c).index(),
            e = $(a).val();
        questionsStack[b].answers[d].right = e;
        var f = $(currQuestion).find(".choices .ranking-right select");
        f.length ? $(f).each(function(a) {
            a = $(f[a]).find("option").get(d);
            $(a).val(e);
            $(a).text(e)
        }) : (a = $(currQuestion).find(".choices li.ranking-right"), a = $(a).get(d), $(a).text(e));
        refreshAnswersList();
        return !0
    },
    sfChangeLeftOption = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent(),
            c = $(c).index();
        a = $(a).val();
        questionsStack[b].answers[c].left = a;
        b = $(currQuestion).find(".choices .ranking-left");
        $(b[c]).text(a);
        refreshAnswersList();
        return !0
    },
    sfChangeChoice = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent(),
            c = $(c).index();
        a = $(a).val();
        questionsStack[b].answers[c].title = a;
        questionsStack[b].choiceStyle ? (b = $(currQuestion).find(".choices option").get(c), $(b).text(a)) : (b = $(currQuestion).find(".choices li").get(c), $(b).find(".choice-value").text(a));
        refreshAnswersList();
        changeAnswersInHides(c);
        return !0
    },
    sfRemoveScale = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent(),
            d = $(c).index();
        questionsStack[b].answers.scales.splice(d - 1, 1);
        questionsStack[b].answers.sid.splice(d - 1, 1);
        $(a).parent().parent().fadeOut(300, function() {
            $(this).remove();
            var a = $(currQuestion).find(".likert-scale th").get(d);
            $(a).remove();
            var b = $(currQuestion).find(".likert-scale tr");
            $(b).each(function(a) {
                a = $(b[a]).find("td").get(d);
                $(a).remove()
            })
        });
        refreshAnswersList();
        removeAnswersInHides(d - 1);
        return !0
    },
    sfRemoveRank = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent(),
            d = $(c).index();
        questionsStack[b].answers.splice(d, 1);
        $(a).parent().parent().fadeOut(300, function() {
            $(this).remove();
            var a = $(currQuestion).find(".choices li.ranking-left"),
                b = $(a[d]).next();
            $(a[d]).remove();
            $(b).remove();
            var c = $(currQuestion).find(".choices li.ranking-right select");
            $(c).each(function(a) {
                a = $(c[a]).find("option").get(d);
                $(a).remove()
            })
        });
        refreshAnswersList();
        removeAnswersInHides(d);
        return !0
    },
    sfRemoveRank2 = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent(),
            d = $(c).index();
        d--;
        questionsStack[b].answers.ranks.splice(d, 1);
        questionsStack[b].answers.rid.splice(d, 1);
        $(a).parent().parent().fadeOut(300, function() {
            $(this).remove();
            var a = $(currQuestion).find(".choices li.ranking-right select");
            $(a).each(function(b) {
                b = $(a[b]).find("option").get(d);
                $(b).remove()
            })
        });
        refreshAnswersList();
        removeAnswersInHides(d);
        return !0
    },
    sfRemoveOption = function(a, b) {
        var c = $(currQuestion).attr("id"),
            d = $(a).parent().parent(),
            e = $(d).index();
        e--;
        questionsStack[c].answers.options.splice(e, 1);
        questionsStack[c].answers.oid.splice(e, 1);
        $(a).parent().parent().fadeOut(300, function() {
            $(this).remove();
            if ("ranking-dropdown" == b) {
                var a = $(currQuestion).find(".choices .ranking-left").get(e),
                    c = $(currQuestion).find(".choices .ranking-right").get(e),
                    d = $(currQuestion).find(".choices .ranking-break").get(e);
                $(a).remove();
                $(c).remove();
                $(d).remove()
            } else "likert-scale" == b && (a = $(currQuestion).find(".likert-scale tr").get(e + 1), $(a).remove())
        });
        refreshAnswersList();
        return !0
    },
    sfRemoveChoice = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent(),
            d = $(c).index();
        questionsStack[b].answers.splice(d, 1);
        var e = questionsStack[b].choiceStyle;
        $(a).parent().parent().fadeOut(300, function() {
            $(this).remove();
            var a = e ? $(currQuestion).find(".choices option").get(d) : $(currQuestion).find(".choices li").get(d);
            $(a).remove()
        });
        refreshAnswersList();
        removeAnswersInHides(d);
        return !0
    },
    sfGetChoicesPanel = function() {
        return '<div class="panel-group" id="panelChoices"><div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-parent="#panelChoices" href="#collapseChoices"><h4>Choices<i class="fa fa-sort-desc" style="float:right;"></i></h4></div><div style="height: auto;" class="panel-collapse collapse in" id="collapseChoices"><div class="panel-body">{PANEL_BODY}</div></div></div></div>'
    },
    sfAddQuestionOption = function(a) {
        var b;
        "" != a && ($(a).insertAfter("#panelProperties"), $("#choices-list").sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            start: function(a, d) {
                b = sfStartIndex(a, d)
            },
            stop: function(a, d) {
                sfChangeChoiceOrdering(a, d, b)
            }
        }), $("#ranking-list").sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            start: function(a, d) {
                b = sfStartIndex(a, d)
            },
            stop: function(a, d) {
                sfChangeRankOrdering(a, d, b)
            }
        }), $("#panelChoices").find(".option a").unbind("click"), $("#panelChoices").find(".option a").click(function() {
            sfRemoveChoice(this)
        }), $("#panelChoices").find(".option input").unbind("keyup"), $("#panelChoices").find(".option input").keyup(function() {
            sfChangeChoice(this)
        }), $("#panelChoices").find(".rank-left input.ranking-text").unbind("keyup"), $("#panelChoices").find(".rank-left input.ranking-text").keyup(function() {
            sfChangeLeftOption(this)
        }), $("#panelChoices").find(".rank-right input.ranking-text").unbind("keyup"), $("#panelChoices").find(".rank-right input.ranking-text").keyup(function() {
            sfChangeRightOption(this)
        }), $("#panelChoices").find(".rank-right a").unbind("click"), $("#panelChoices").find(".rank-right a").click(function() {
            sfRemoveRank(this)
        }), bindRankEvents(), bindLikertEvents(), $.fn.selectpicker && $(".selectpicker").selectpicker());
        return !0
    },
    bindLikertEvents = function() {
        $("#panelChoices").find("#scale-list .scale .scale-text").unbind("keyup");
        $("#panelChoices").find("#scale-list .scale .scale-text").keyup(function() {
            sfChangeScale(this)
        });
        $("#panelChoices").find("#option-list-likert .options .option-text").unbind("keyup");
        $("#panelChoices").find("#option-list-likert .options .option-text").keyup(function() {
            sfChangeOption(this, "likert-scale")
        });
        $("#panelChoices").find("#scale-list .scale a.scale-remove").unbind("click");
        $("#panelChoices").find("#scale-list .scale a.scale-remove").click(function() {
            sfRemoveScale(this)
        });
        $("#panelChoices").find("#option-list-likert .options a.option-remove").unbind("click");
        $("#panelChoices").find("#option-list-likert .options a.option-remove").click(function() {
            sfRemoveOption(this, "likert-scale")
        });
        $("#scale-list").sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            start: function(a, b) {
                startIndex = sfStartIndex(a, b)
            },
            stop: function(a, b) {
                sfChangeScaleOrdering(a, b, startIndex)
            }
        });
        $("#option-list-likert").sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            start: function(a, b) {
                startIndex = sfStartIndex(a, b)
            },
            stop: function(a, b) {
                sfChangeOptionOrdering(a, b, startIndex, "likert-scale")
            }
        })
    },
    bindRankEvents = function() {
        $("#panelChoices").find("#rank-list .rank .rank-text").unbind("keyup");
        $("#panelChoices").find("#rank-list .rank .rank-text").keyup(function() {
            sfChangeRank(this)
        });
        $("#panelChoices").find("#option-list-ranking .options .option-text").unbind("keyup");
        $("#panelChoices").find("#option-list-ranking .options .option-text").keyup(function() {
            sfChangeOption(this, "ranking")
        });
        $("#panelChoices").find("#rank-list .rank a.rank-remove").unbind("click");
        $("#panelChoices").find("#rank-list .rank a.rank-remove").click(function(a) {
            sfRemoveRank2(this)
        });
        $("#panelChoices").find("#option-list-ranking .options a.option-remove").unbind("click");
        $("#panelChoices").find("#option-list-ranking .options a.option-remove").click(function() {
            sfRemoveOption(this, "ranking")
        });
        $("#rank-list").sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            start: function(a, b) {
                startIndex = sfStartIndex(a, b)
            },
            stop: function(a, b) {
                sfChangeRankOrdering2(a, b, startIndex)
            }
        });
        $("#option-list-ranking").sortable({
            axis: "y",
            placeholder: "ui-state-highlight",
            cursor: "move",
            start: function(a, b) {
                startIndex = sfStartIndex(a, b)
            },
            stop: function(a, b) {
                sfChangeOptionOrdering(a, b, startIndex, "ranking")
            }
        })
    },
    sfAddRank = function(a) {
        var b = $(currQuestion).attr("id");
        if ("ranking-dropdown" == a) {
            questionsStack[b].answers.ranks.push("New rank");
            questionsStack[b].answers.rid.push("");
            b = '<li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" value=" New rank" class="rank-text"><a href="#" class="remove rank-remove"><i class="fa fa-times"></i></a></div></li>';
            $("#rank-list").append(b);
            var c = $(currQuestion).find(".choices .ranking-right select");
            $(c).each(function(a) {
                $(c[a]).append('<option value="New rank">New rank</option>')
            });
            bindRankEvents(a)
        } else if ("likert-scale" == a) {
            questionsStack[b].answers.scales.push("New scale");
            questionsStack[b].answers.sid.push("");
            b = '<li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" value="New scale" class="scale-text"><a href="#" class="remove scale-remove"><i class="fa fa-times"></i></a></div></li>';
            $("#scale-list").append(b);
            (b = $(currQuestion).find(".likert-scale tr:first")) ? $(b).append("<th>New scale</th>"): $(".likert-scale").append("<thead><tr><th></th><th>New scale</th></tr></thead><tbody></tbody>");
            var d = $(currQuestion).find(".likert-scale tr").not("tr:first");
            d.length && $(d).each(function(a) {
                var b = '<td class="ui-selectee"><label class="clean-input-wrap ui-selectee"><input type="radio" name="' + $(d[a]).attr("nameid") + '" class="ui-selectee"><span class="clean-input ui-selectee"></span></label></td>';
                $(d[a]).append(b)
            });
            bindLikertEvents(a)
        }
        refreshAnswersList();
        return !0
    },
    sfAddOption = function(a) {
        var b = $(currQuestion).attr("id");
        questionsStack[b].answers.options.push("New option");
        questionsStack[b].answers.oid.push("");
        if ("ranking-dropdown" == a) {
            $("#option-list-ranking").append('<li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" value="New option" class="option-text"><a href="#" class="remove option-remove"><i class="fa fa-times"></i></a></div></li>');
            a = questionsStack[b].answers.ranks;
            if (a.length) {
                for (var c = '<select class="ui-selectee">', b = 0; b < a.length; b++) c += '<option value="' + a[b] + '">' + a[b] + "</option>";
                c += "</select>"
            }
            c = '<li class="ranking-left ui-selectee">New option</li><li class="ranking-right ui-selectee">' + c + '</li><li class="ranking-break ui-selectee"></li>';
            $(currQuestion).find(".choices").append(c)
        } else {
            $("#option-list-likert").append('<li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" value="New option" class="option-text"><a href="#" class="remove option-remove"><i class="fa fa-times"></i></a></div></li>');
            var c = $(currQuestion).find(".likert-scale tbody"),
                d = sfGenerateID(),
                e = '<tr nameid="' + d + '"><td>New option</td>';
            a = $(currQuestion).find(".likert-scale thead th").not("th:first");
            a.length && $(a).each(function(a) {
                e += '<td><label class="clean-input-wrap ui-selectee"><input type="radio" class="ui-selectee" name="' + d + '"><span class="clean-input ui-selectee"></span></label></td>'
            });
            e += "</tr>";
            $(c).append(e)
        }
        bindRankEvents();
        refreshAnswersList();
        return !0
    },
    sfAddRanking = function(a, b, c) {
        var d = '<li class="ui-sortable-handle"><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="' + a + '"></div><div class="rank-right"><input type="text" class="ranking-text" value="' + b + '"><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
        $(".text-list").append(d);
        $("#panelChoices").find(".rank-right a.rank-remove").click(function() {
            sfRemoveRank(this)
        });
        $("#panelChoices").find(".rank-left input.ranking-text").keyup(function() {
            sfChangeLeftOption(this)
        });
        $("#panelChoices").find(".rank-right input.ranking-text").keyup(function() {
            sfChangeRightOption(this)
        });
        d = $(currQuestion).attr("id");
        questionsStack[d].answers.push({
            left: a,
            right: b,
            leftid: "",
            rightid: ""
        });
        "ranking" == c && (d = $(currQuestion).find(".choices li.ranking-right").get(0), d = $(d).html(), null == d && (d = "<select></select>"), d = '<li class="ranking-left ui-selectee">' + a + '</li><li class="ranking-right ui-selectee">' + d + '</li><li class="ranking-break ui-selectee"></li>', $(currQuestion).find(".choices").append(d), d = '<option value="' + b + '">' + b + "</option>", $(currQuestion).find(".choices .ranking-right select").append(d));
        "ranking-dragdrop" == c && (d = '<li class="ranking-left fixed ui-selectee">' + a + '</li><li class="ranking-right dragable ui-selectee ui-widget-header">' + b + '</li><li class="ranking-break ui-selectee"></li>', $(currQuestion).find(".choices").append(d), $(".dragable").draggable({
            containment: $(currQuestion),
            scroll: !1
        }));
        refreshAnswersList();
        return !0
    },
    sfAddChoice = function(a, b, c) {
        a = a ? a : "Choice Text";
        if ("undefined" != typeof b ? b : 1) b = '<li class="ui-sortable-handle" style=""><div class="option"><a href="#" class="remove"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" value="' + a + '" class="text"></div></li>', $("#choices-list").append(b), $("#panelChoices").find(".option a").click(function() {
            sfRemoveChoice(this)
        }), $("#panelChoices").find(".option input").keyup(function() {
            sfChangeChoice(this)
        });
        b = $(currQuestion).attr("id");
        questionsStack[b].answers.push({
            title: a,
            id: ""
        });
        var d = $(currQuestion).find(".choices").attr("data-id");
        c = "pick-one" == c ? "radio" : "checkbox";
        questionsStack[b].choiceStyle ? (a = "<option>" + a + "</option>", $(currQuestion).find(".choices").append(a)) : (a = '<li class="choice ui-selectee"><label class="ui-selectee"><label class="clean-input-wrap ui-selectee"><input type="' + c + '" name="' + d + '" class="ui-selectee"><span class="clean-input ui-selectee"></span></label> <span class="choice-value ui-selectee">' + a + "</span></label></li>", $("#" + b + " .choices .other").length ? $(a).insertBefore("#" + b + " .choices .other") : $(currQuestion).find(".choices").append(a));
        refreshAnswersList();
        return !0
    },
    sfCollectQuestions = function(a) {
        var b = $(a).attr("name").replace("section_", "");
        questionsStack[b].sections = [];
        a = $(a).find("option:selected");
        if (a.length)
            for (var c = 0; c < a.length; c++) questionsStack[b].sections.push($(a[c]).val())
    },
    sfSelectOption = function(a) {
        a = $(a).parent().find("option");
        if (a.length)
            for (var b = 0; b < a.length; b++) {
                var c = a[b];
                $(c).prop("selected") ? $(c).attr("selected", "selected") : $(c).removeAttr("selected")
            }
    },
    sfGetOptions = function(a) {
        var b = $(currQuestion).attr("name");
        if (questionsStack[a].exists) switch (b) {
            case "boilerplate":
                $("#question-type").html("Boilerplate");
                break;
            case "short-answer":
                $("#question-type").html("Short answer");
                break;
            case "section-separator":
                $("#question-type").html("Section separator");
                b = '<select multiple="multiple" size="8" name="section_' + a + '" style="width:90%;" onclick="javascript:sfCollectQuestions(this);">';
                for (c in questionsStack) selected = questionsStack[a].sections.length ? sf_inArray(questionsStack[a].sections, c) ? 'selected="selected"' : "" : "", "section-separator" !== questionsStack[c].sf_qtype && (b += '<option value="' + c + '" ' + selected + ' onclick="javascript:sfSelectOption(this);">' + questionsStack[c].sf_qtitle + "</option>");
                b += "</select>";
                d = sfGetChoicesPanel();
                d = d.replace("{PANEL_BODY}", b);
                break;
            case "pick-one":
                $("#question-type").html("Pick one");
                d = sfGetChoicesPanel();
                e = sfGetChoices(a, "pick-one");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "pick-many":
                $("#question-type").html("Pick many");
                d = sfGetChoicesPanel();
                e = sfGetChoices(a, "pick-many");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "ranking":
                $("#question-type").html("Ranking");
                d = sfGetChoicesPanel();
                e = sfGetRanking(a, "ranking");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "ranking-dragdrop":
                $("#question-type").html("Ranking drag&drop");
                d = sfGetChoicesPanel();
                e = sfGetRanking(a, "ranking-dragdrop");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "ranking-dropdown":
            case "likert-scale":
                d = sfGetChoicesPanel(), "ranking-dropdown" == b ? ($("#question-type").html("Ranking-dropdown"), e = sfGetRanking(a, "ranking-dropdown")) : "likert-scale" == b && ($("#question-type").html("Likert scale"), e = sfGetRanking(a, "likert-scale")), d = d.replace("{PANEL_BODY}", e)
        } else switch (questionsStack[a].exists = 1, questionsStack[a].sf_qdescription = "", questionsStack[a].sf_iscale = "", questionsStack[a].published = 1, questionsStack[a].sf_compulsory = 0, questionsStack[a].sf_default_hided = 0, questionsStack[a].is_final_question = 0, b) {
            case "section-separator":
                $("#question-type").html("Section separator");
                questionsStack[a].sf_qtype = "section-separator";
                questionsStack[a].sf_qtitle = "Section Heading";
                questionsStack[a].sections = [];
                var b = '<select multiple="multiple" size="8" name="section_' + a + '" style="width:90%;" onclick="javascript:sfCollectQuestions(this);">',
                    c;
                for (c in questionsStack) "section-separator" !== questionsStack[c].sf_qtype && (b += '<option value="' + c + '" onclick="javascript:sfSelectOption(this);">' + questionsStack[c].sf_qtitle + "</option>");
                var b = b + "</select>",
                    d = sfGetChoicesPanel(),
                    d = d.replace("{PANEL_BODY}", b);
                break;
            case "pick-one":
                $("#question-type").html("Pick one");
                questionsStack[a].sf_qtype = "pick-one";
                questionsStack[a].sf_qtitle = "Question Text";
                questionsStack[a].choiceStyle = 0;
                questionsStack[a].answers = [];
                questionsStack[a].answers.push({
                    title: "Choice 1",
                    id: ""
                });
                questionsStack[a].answers.push({
                    title: "Choice 2",
                    id: ""
                });
                var d = sfGetChoicesPanel(),
                    e;
                e = '<ul class="text-list" id="choices-list"><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 1"></div></li><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 2"></div></li></ul>' + sfGetChoicesTools(a, "pick-one");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "pick-many":
                $("#question-type").html("Pick many");
                questionsStack[a].sf_qtype = "pick-many";
                questionsStack[a].sf_qtitle = "Question Text";
                questionsStack[a].choiceStyle = 0;
                questionsStack[a].answers = [];
                questionsStack[a].answers.push({
                    title: "Choice 1",
                    id: ""
                });
                questionsStack[a].answers.push({
                    title: "Choice 2",
                    id: ""
                });
                d = sfGetChoicesPanel();
                e = '<ul class="text-list" id="choices-list"><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 1"></div></li><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 2"></div></li></ul>' + sfGetChoicesTools(a, "pick-many");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "short-answer":
                $("#question-type").html("Short answer");
                questionsStack[a].sf_qtype = "short-answer";
                questionsStack[a].sf_qtitle = "Every {x} in question text will be replaced by input box. If the number of {x} is more than zero no large text area will be displayed. To place text area with input box in question text use {y} tag.";
                d = "";
                break;
            case "ranking":
                $("#question-type").html("Ranking");
                questionsStack[a].sf_qtype = "ranking";
                questionsStack[a].sf_qtitle = "Question Text";
                questionsStack[a].answers = [];
                questionsStack[a].answers.push({
                    left: "Option 1",
                    right: "Rank 1",
                    leftid: "",
                    rightid: ""
                });
                questionsStack[a].answers.push({
                    left: "Option 2",
                    right: "Rank 2",
                    leftid: "",
                    rightid: ""
                });
                d = sfGetChoicesPanel();
                e = '<ul class="text-list" id="ranking-list"><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 1" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 1" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 2" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 2" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>' + sfGetRankingTools(a, "ranking");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "ranking-dragdrop":
                $("#question-type").html("Ranking drag&drop");
                questionsStack[a].sf_qtype = "ranking-dragdrop";
                questionsStack[a].sf_qtitle = "Question Text";
                questionsStack[a].answers = [];
                questionsStack[a].answers.push({
                    left: "Option 1",
                    right: "Rank 1",
                    leftid: "",
                    rightid: ""
                });
                questionsStack[a].answers.push({
                    left: "Option 2",
                    right: "Rank 2",
                    leftid: "",
                    rightid: ""
                });
                d = sfGetChoicesPanel();
                e = '<ul class="text-list" id="ranking-list"><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 1" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 1" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 2" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 2" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>' + sfGetRankingTools(a, "ranking-dragdrop");
                d = d.replace("{PANEL_BODY}", e);
                break;
            case "boilerplate":
                $("#question-type").html("Boilerplate");
                questionsStack[a].sf_qtype = "boilerplate";
                questionsStack[a].sf_qtitle = "Boilerplate";
                d = "";
                break;
            case "ranking-dropdown":
            case "likert-scale":
                "ranking-dropdown" == b ? ($("#question-type").html("Ranking-dropdown"), questionsStack[a].sf_qtype = "ranking-dropdown", questionsStack[a].sf_qtitle = "Question Text", questionsStack[a].answers = {
                    ranks: ["1", "2"],
                    options: ["Option 1", "Option 2"],
                    oid: ["", ""],
                    rid: ["", ""]
                }) : "likert-scale" == b && ($("#question-type").html("Likert scale"), questionsStack[a].sf_qtype = "likert-scale", questionsStack[a].sf_qtitle = "Question Text", questionsStack[a].answers = {
                    scales: ["Scale 1", "Scale 2", "Scale 3", "Scale 4"],
                    options: ["Option 1", "Option 2"],
                    oid: ["", ""],
                    sid: ["", "", "", ""]
                }), d = sfGetChoicesPanel(), "ranking-dropdown" == b ? e = '<ul class="text-list" id="rank-list"><li class="rank-title">Ranks</li><li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" class="rank-text" value="1" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" class="rank-text" value="2" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>' + sfGetRankTools("rank", "ranking-dropdown") : "likert-scale" == b && (e = '<ul class="text-list" id="scale-list"><li class="scale-title">Scales</li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 1" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 2" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 3" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 4" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>' + sfGetRankTools("rank", "likert-scale")), e += '<hr/><ul class="text-list" id="' + ("ranking-dropdown" == b ? "option-list-ranking" : "option-list-likert") + '"><li class="option-title">Answer Options</li><li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="Option 1" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="Option 2" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>', "ranking-dropdown" == b ? e += sfGetRankTools("option", "ranking-dropdown") : "likert-scale" == b && (e += sfGetRankTools("option", "likert-scale")), d = d.replace("{PANEL_BODY}", e)
        }
        $("#collapseProperties").hasClass("in") || $("#collapseProperties").addClass("in");
        sfCollapseNewQuestion();
        sfRemoveChoicesPanel();
        sfSetOptionsFields(a);
        sfEnableOptions();
        sfAddQuestionOption(d);
        $(".tab-pane").removeClass("active");
        $(".nav-tabs li").removeClass("active");
        $("#questionsButton").addClass("active");
        $("#questions").addClass("active");
        $(".dragable").draggable({
            containment: $(currQuestion),
            scroll: !1
        });
        refreshQuestionsList();
        refreshAnswersList();
        $("#sf_qtitle").focus()
    },
    sfCollapseNewQuestion = function() {
        $("#basic").removeClass("in")
    },
    sfChangeScale = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).val();
        a = $(a).parent().parent().index();
        questionsStack[b].answers.scales[a - 1] = c;
        b = $(currQuestion).find(".likert-scale th").get(a);
        $(b).html(c);
        refreshAnswersList();
        changeAnswersInHides(a - 1);
        return !0
    },
    sfChangeRank = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).val(),
            d = $(a).parent().parent().index();
        d--;
        questionsStack[b].answers.ranks[d] = c;
        var e = $(currQuestion).find(".choices .ranking-right select");
        $(e).each(function(a) {
            a = $(e[a]).find("option");
            a = $(a).get(d);
            $(a).val(c);
            $(a).text(c)
        });
        refreshAnswersList();
        changeAnswersInHides(d);
        return !0
    },
    sfChangeOption = function(a, b) {
        var c = $(currQuestion).attr("id"),
            d = $(a).val(),
            e = $(a).parent().parent().index();
        e--;
        questionsStack[c].answers.options[e] = d;
        "ranking-dropdown" == b ? (c = $(currQuestion).find(".choices .ranking-left"), e = $(c).get(e), $(e).html(d)) : "likert-scale" == b && (e = $(currQuestion).find(".likert-scale tr").get(e + 1), $(e).find("td:first").html(d));
        refreshAnswersList();
        return !0
    },
    sfGetRankTools = function(a, b) {
        switch (a) {
            case "rank":
                var c = "sfAddRank('" + b + "')";
                break;
            case "option":
                c = "sfAddOption('" + b + "')"
        }
        return ohtml = '<hr/><div class="choices-toolbar option-toolbar"><a class="toolbox-action button button-small" onclick="javascript:' + c + ';"><i class="fa fa-plus-circle">&nbsp;Add</i></a></div>'
    },
    sfGetRankingTools = function(a, b) {
        switch (b) {
            case "ranking":
				addFunc = "javascript:sfAddRanking('Option Text', 'Rank Text', 'ranking');";
                break;
            case "ranking-dragdrop":
                addFunc = "javascript:sfAddRanking('Option Text', 'Rank Text', 'ranking-dragdrop');"
        }
        return '<hr/><div class="choices-toolbar ranking-toolbar"><a class="toolbox-action button button-small" onclick="' + addFunc + '"><i class="fa fa-plus-circle">&nbsp;Add</i></a></div>'
    },
    sfGetChoicesTools = function(a, b) {
        if (questionsStack[a].answers[0].other_option) var c = 'checked="checked"',
            d = questionsStack[a].answers[0].other_option_text;
        else d = c = "";
        var e = "",
            f = "";
        questionsStack[a].choiceStyle ? f = "selected='selected'" : e = "selected='selected'";
        switch (b) {
            case "pick-one":
                addFunc = "javascript:sfAddChoice('Choice Text', true, 'pick-one');";
                break;
            case "pick-many":
                addFunc = "javascript:sfAddChoice('Choice Text', true, 'pick-many');"
        }
        c = '<hr/><div class="choices-toolbar"><a class="toolbox-action button button-small" onclick="' + addFunc + '"><i class="fa fa-plus-circle">&nbsp;Add</i></a><a class="toolbox-action button button-small" onclick="javascript:sfDialogBulkList(\'' + b + '\');"><i class="fa fa-list">&nbsp;Bulk</i></a></div><br/><ul class="text-list"><li id="sf_other"><input type="checkbox" name="other_option_cb" id="other_option_cb" class="css-checkbox" onclick="javascript:sfToggleOtherOption(\'' + b + "');\" " + c + '/><label class="css-label cb0" for="other_option_cb">Others option</label><input type="text" name="other_option" id="other_option" class="input-large" placeholder="Others option" onkeyup="javascript:sfChangeOtherOption();" value="' + d + '"/></li>';
        "pick-one" == b && (c += '<li><label for="sf_qstyle" class="use-dropdown">Use drop-down style:</label><select data-style="btn" id="sf_qstyle" name="sf_qstyle" class="form-control" onchange="javascript:sfChangeStyle(this)"><option value="0" ' + e + '>No</option><option value="1" ' + f + ">Yes</option></select></li>");
        return c + "</ul>"
    },
    sfChangeStyle = function(a) {
        var b = $(currQuestion).attr("id");
        a = $(a).val();
        if (0 == a) {
            questionsStack[b].choiceStyle = 0;
            dataId = $(currQuestion).find(".choices").attr("data-id");
            var c = '<ul class="choices" data-id="' + dataId + '"></ul>';
            $(currQuestion).find(".choices").remove();
            $(currQuestion).append(c);
            if (questionsStack[b].answers.length) {
                c = sfClone(questionsStack[b]);
                delete questionsStack[b].answers;
                questionsStack[b].answers = [];
                for (var d = 0; d < c.answers.length; d++) sfAddChoice(c.answers[d].title, !1, "pick-one")
            }
        }
        1 == a && (questionsStack[b].choiceStyle = 1, sfAddToDropDown());
        return !0
    },
    sfAddToDropDown = function() {
        dataId = $(currQuestion).find(".choices").attr("data-id");
        $(currQuestion).find(".choices").remove();
        var a = $(currQuestion).attr("id"),
            a = questionsStack[a].answers;
        shtml = '<select class="choices" data-id="' + dataId + '">';
        if (a.length)
            for (var b = 0; b < a.length; b++) shtml += "<option>" + a[b].title + "</option>";
        shtml += "</select>";
        $(currQuestion).append(shtml);
        return !0
    },
    sfGetRanking = function(a, b) {
        if ("ranking-dropdown" == b) {
            var c = '<ul class="text-list" id="rank-list"><li class="rank-title">Ranks</li>',
                d = questionsStack[a].answers.ranks;
            if (d.length)
                for (var e = 0; e < d.length; e++) c += '<li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" class="rank-text" value="' + d[e] + '" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
            c = c + "</ul>" + sfGetRankTools("rank", b);
            c += '<hr/><ul class="text-list" id="option-list-ranking"><li class="option-title">Answer Options</li>';
            d = questionsStack[a].answers.options;
            if (d.length)
                for (e = 0; e < d.length; e++) c += '<li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="' + d[e] + '" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
            c += "</ul>";
            c += sfGetRankTools("option", b)
        } else if ("likert-scale" == b) {
            c = '<ul class="text-list" id="scale-list"><li class="scale-title">Scales</li>';
            d = questionsStack[a].answers.scales;
            if (d.length)
                for (e = 0; e < d.length; e++) c += '<li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="' + d[e] + '" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
            c += "</ul>";
            c += sfGetRankTools("rank", b);
            c += '<hr/><ul class="text-list" id="option-list-likert"><li class="option-title">Answer Options</li>';
            d = questionsStack[a].answers.options;
            if (d.length)
                for (e = 0; e < d.length; e++) c += '<li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="' + d[e] + '" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
            c += "</ul>";
            c += sfGetRankTools("option", b)
        } else {
            c = '<ul class="text-list" id="ranking-list">';
            d = questionsStack[a].answers;
            if (d.length)
                for (e = 0; e < d.length; e++) c += '<li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="' + d[e].left + '" /></div><div class="rank-right"><input type="text" class="ranking-text" value="' + d[e].right + '" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
            c += "</ul>";
            c += sfGetRankingTools(a, b)
        }
        return c
    },
    sfGetChoices = function(a, b) {
        var c = '<ul class="text-list" id="choices-list">',
            d = questionsStack[a].answers;
        if (d.length)
            for (var e = 0; e < d.length; e++) c += '<li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" value="' + d[e].title + '"></div></li>';
        return c = c + "</ul>" + sfGetChoicesTools(a, b)
    },
    sfToggleOtherOption = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $("#" + b).find("ul.choices").attr("data-id");
        if ($("#other_option_cb").prop("checked")) {
            $("#other_option").val("Other, please specify...");
            if ("pick-one" == a) var d = '<li class="choice other"><label><label class="clean-input-wrap"><input type="radio" name="' + c + '"><span class="clean-input"></span></label> <span class="choice-value">Other, please specify...</span><input type="text" "="" class="text dummy"></label></li>';
            "pick-many" == a && (d = '<li class="choice other"><label><label class="clean-input-wrap"><input type="checkbox" name="' + c + '"><span class="clean-input"></span></label> <span class="choice-value">Other, please specify...</span><input type="text" "="" class="text dummy"></label></li>');
            $("#" + b + " .choices").append(d);
            questionsStack[b].answers[0].other_option = 1;
            questionsStack[b].answers[0].other_option_text = "Other, please specify..."
        } else $("#" + b).find("li.other").remove(), questionsStack[b].answers[0].other_option = 0, questionsStack[b].answers[0].other_option_text = "", $("#other_option").val("");
        return !0
    },
    sfChangeOtherOption = function() {
        var a = $(currQuestion).attr("id"),
            b = $("#other_option").val();
        $(currQuestion).find("ul.choices .other .choice-value").text(b);
        questionsStack[a].answers[0].other_option_text = b;
        return !0
    },
    sfRemoveChoicesPanel = function() {
        $("#panelChoices").remove();
        return !0
    },
    sfStartIndex = function(a, b) {
        return $(b.item).index()
    },
    sfChangeScaleOrdering = function(a, b, c) {
        var d = $(currQuestion).attr("id"),
            e = $(b.item).prev().index(),
            f = $(b.item).next().index();
        a = $(currQuestion).find(".likert-scale thead th").get(c);
        b = $(currQuestion).find(".likert-scale thead tr");
        var g = $(currQuestion).find(".likert-scale tbody tr");
        if (0 <= e) {
            if (c > e) {
                var h = $(b).find("th").get(e);
                $(a).insertAfter(h)
            }
            c <= e && (h = $(b).find("th").get(e + 1), $(a).insertAfter(h))
        } else b = $(b).find("th").get(f - 1), $(a).insertBefore(b);
        g.length && $(g).each(function(a) {
            var b = $(g[a]).find("td").get(c);
            if (0 <= e) {
                if (c > e) {
                    var d = $(g[a]).find("td").get(e);
                    $(b).insertAfter(d)
                }
                c <= e && (d = $(g[a]).find("td").get(e + 1), $(b).insertAfter(d))
            } else a = $(g[a]).find("td").get(f - 1), $(b).insertBefore(a)
        });
        var k = $("#scale-list .scale .scale-text");
        $(k).each(function(a) {
            var b = $(k[a]).val();
            questionsStack[d].answers.scales[a] = b
        });
        refreshAnswersList()
    },
    sfChangeOptionOrdering = function(a, b, c, d) {
        if ($(b).hasClass("option-title")) return !1;
        var e = $(currQuestion).attr("id");
        a = $(b.item).prev().index();
        b = $(b.item).next().index();
        a--;
        b--;
        c--;
        if ("ranking-dropdown" == d) {
            var f = $(currQuestion).find(".choices .ranking-left"),
                g = $(currQuestion).find(".choices .ranking-right"),
                h = $(currQuestion).find(".choices .ranking-break");
            d = $(f).get(c);
            var g = $(g).get(c),
                k = $(h).get(c);
            0 <= a ? (c > a && (b = $(h).get(a), $(k).insertAfter(b), $(g).insertAfter(b), $(d).insertAfter(b)), c <= a && (b = $(h).get(a + 1), $(k).insertAfter(b), $(g).insertAfter(b), $(d).insertAfter(b))) : (c = $(f).get(b - 1), $(d).insertBefore(c), $(g).insertBefore(c), $(k).insertBefore(c));
            qRank = $("#rank-list .rank .rank-text");
            qOption = $("#option-list-ranking .options .option-text");
            $(qRank).each(function(a) {
                var b = $(qRank[a]).val();
                questionsStack[e].answers.ranks[a] = b
            });
            $(qOption).each(function(a) {
                var b = $(qOption[a]).val();
                questionsStack[e].answers.options[a] = b
            })
        } else "likert-scale" == d && (dTr = $(currQuestion).find(".likert-scale tbody tr").get(c), 0 <= a ? (c > a && (b = $(currQuestion).find(".likert-scale tbody tr").get(a), $(dTr).insertAfter(b)), c <= a && (b = $(currQuestion).find(".likert-scale tbody tr").get(a + 1), $(dTr).insertAfter(b))) : (c = $(currQuestion).find(".likert-scale tbody tr").get(b - 1), $(dTr).insertBefore(c)), qOption = $("#option-list-likert .options .option-text"), $(qOption).each(function(a) {
            var b = $(qOption[a]).val();
            questionsStack[e].answers.options[a] = b
        }));
        refreshAnswersList();
        return !0
    },
    sfChangeRankOrdering2 = function(a, b, c) {
        if ($(b).hasClass("rank-title")) return !1;
        var d = $(currQuestion).attr("id"),
            e = $(currQuestion).find(".choices .ranking-right select"),
            f = $(b.item).prev().index(),
            g = $(b.item).next().index();
        f--;
        g--;
        c--;
        $(e).each(function(a) {
            var b = $(e[a]).find("option");
            a = $(b).get(c);
            if (0 <= f) {
                if (c > f) {
                    var d = $(b).get(f);
                    $(a).insertAfter(d)
                }
                c <= f && (d = $(b).get(f + 1), $(a).insertAfter(d))
            } else b = $(b).get(g - 1), $(a).insertBefore(b)
        });
        ranks = $("#rank-list div.rank .rank-text");
        $(ranks).each(function(a) {
            var b = $(ranks[a]).val();
            questionsStack[d].answers.ranks[a] = b
        });
        refreshAnswersList();
        return !0
    },
    sfChangeRankOrdering = function(a, b, c) {
        var d = $(currQuestion).attr("id"),
            e = $(currQuestion).find(".choices li.ranking-left"),
            f = $(currQuestion).find(".choices li.ranking-right"),
            g = $(currQuestion).find(".choices li.ranking-break");
        a = $(e).get(c);
        var f = $(f).get(c),
            h = $(g).get(c),
            k = $(b.item).prev().index();
        b = $(b.item).next().index();
        0 <= k ? (c > k && (b = $(g).get(k), $(h).insertAfter(b), $(f).insertAfter(b), $(a).insertAfter(b)), c <= k && (b = $(g).get(k + 1), $(h).insertAfter(b), $(f).insertAfter(b), $(a).insertAfter(b))) : (c = $(e).get(b - 1), $(a).insertBefore(c), $(f).insertBefore(c), $(h).insertBefore(c));
        qLeft = $("#ranking-list li div.rank-left .ranking-text");
        qRight = $("#ranking-list li div.rank-right .ranking-text");
        $(qLeft).each(function(a) {
            var b = $(qLeft[a]).val(),
                c = $(qRight[a]).val();
            questionsStack[d].answers[a].left = b;
            questionsStack[d].answers[a].right = c
        });
        refreshAnswersList()
    },
    sfChangeChoiceOrdering = function(a, b, c) {
        var d = $(currQuestion).attr("id"),
            e = questionsStack[d].choiceStyle,
            f = e ? $(currQuestion).find(".choices option") : $(currQuestion).find(".choices li");
        a = $(f).get(c);
        var g = $(b.item).prev().index();
        b = $(b.item).next().index();
        0 <= g ? (c > g && (b = $(f).get(g), $(a).insertAfter(b)), c <= g && (b = $(f).get(g + 1), $(a).insertAfter(b))) : (c = $(f).get(b - 1), $(a).insertBefore(c));
        f = e ? $(currQuestion).find(".choices option") : $(currQuestion).find(".choices li");
        $(f).each(function(a) {
            var b = e ? $(f[a]).text() : $(f[a]).find(".choice-value").text();
            questionsStack[d].answers[a].title = b
        });
        refreshAnswersList();
        return !0
    },
    sfInsertQuestion = function(a, b) {
        $(".placeholder").remove();
        $("#survey-questions" + currPage).append(a);
        sfClearActives();
        $("#" + b).addClass("active");
        $("#" + b + " i.remove").css("opacity", "1");
        $("#survey-questions" + currPage).selectable();
        $("#" + b).click(function() {
            sfClearActives();
            $(this).addClass("active");
            $(this).find("i.remove").css("opacity", "1");
            currQuestion = $(this);
            sfGetOptions(b);
            return !0
        });
        currQuestion = $("#" + b);
        questionsStack[b] = {};
        questionsStack[b].exists = 0;
        questionsStack[b].page = currPage;
        questionsStack[b].hides = [];
        questionsStack[b].rules = [];
        questionsStack[b].questOrdering = "";
        sfGetOptions(b);
        questOrdering++;
        sf_SortQuestions();
        return !0
    },
    sfClearActives = function() {
        $("ol.page li").removeClass("active");
        $("ol.page i.remove").css("opacity", "0.2")
    },
    refreshQuestionsList = function() {
        var a = $(currQuestion).attr("id");
        if (questionsStack) {
            var b = $("#sf_quest_list2");
            b.html("");
            var c = $("#sf_quest_list3");
            c.html("");
            var d = $("#sf_quest_list");
            d.html("");
            var e = '<option value="0">- Select question -</option>',
                f;
            for (f in questionsStack)
                if (f != a && "short-answer" != questionsStack[f].sf_qtype && "boilerplate" != questionsStack[f].sf_qtype && "section-separator" != questionsStack[f].sf_qtype && "page-break" != questionsStack[f].sf_qtype) var g = questionsStack[f].sf_qtitle,
                    g = strip_tags(g),
                    g = 30 < g.length ? g.substr(0, 30) + "..." : g,
                    e = e + ('<option value="' + f + '">' + g + "</option>");
            b.html(e);
            c.html(e);
            d.html(e)
        }
    };

function strip_tags(a) {
    return a.replace(/<\/?[^>]+>/gi, "")
}
var refreshAnswersList = function() {
        if (currQuestion) {
            var a = !1,
                b = $(currQuestion).attr("id"),
                c = '<option value="0">- Select answer -</option>',
                d = $("#sf_field_list"),
                e = $("#sf_option_list");
            d.html("");
            e.html("");
            if (b) switch (questionsStack[b].sf_qtype) {
                case "pick-one":
                case "pick-many":
                    if (questionsStack[b].answers.length)
                        for (var f = 0; f < questionsStack[b].answers.length; f++) var g = 30 < questionsStack[b].answers[f].title.length ? questionsStack[b].answers[f].title.substr(0, 30) + "..." : questionsStack[b].answers[f].title,
                            c = c + ('<option value="' + (f + 1) + '">' + g + "</option>");
                    break;
                case "ranking":
                case "ranking-dragdrop":
                    a = !0;
                    option = '<option value="0">- Select option -</option>';
                    if (questionsStack[b].answers.length)
                        for (f = 0; f < questionsStack[b].answers.length; f++) {
                            var g = 30 < questionsStack[b].answers[f].right.length ? questionsStack[b].answers[f].right.substr(0, 30) + "..." : questionsStack[b].answers[f].right,
                                h = 30 < questionsStack[b].answers[f].left.length ? questionsStack[b].answers[f].left.substr(0, 30) + "..." : questionsStack[b].answers[f].left,
                                c = c + ('<option value="' + (f + 1) + '">' + g + "</option>");
                            option += '<option value="' + (f + 1) + '">' + h + "</option>"
                        }
                    break;
                case "ranking-dropdown":
                    a = !0;
                    option = '<option value="0">- Select option -</option>';
                    if (questionsStack[b].answers.ranks.length)
                        for (f = 0; f < questionsStack[b].answers.ranks.length; f++) g = 30 < questionsStack[b].answers.ranks[f].length ? questionsStack[b].answers.ranks[f].substr(0, 30) + "..." : questionsStack[b].answers.ranks[f], c += '<option value="' + (f + 1) + '">' + g + "</option>";
                    if (questionsStack[b].answers.options.length)
                        for (f = 0; f < questionsStack[b].answers.options.length; f++) h = 30 < questionsStack[b].answers.options[f].length ? questionsStack[b].answers.options[f].substr(0, 30) + "..." : questionsStack[b].answers.options[f], option += '<option value="' + (f + 1) + '">' + h + "</option>";
                    break;
                case "likert-scale":
                    a = !0;
                    option = '<option value="0">- Select option -</option>';
                    if (questionsStack[b].answers.scales.length)
                        for (f = 0; f < questionsStack[b].answers.scales.length; f++) g = 30 < questionsStack[b].answers.scales[f].length ? questionsStack[b].answers.scales[f].substr(0, 30) + "..." : questionsStack[b].answers.scales[f], c += '<option value="' + (f + 1) + '">' + g + "</option>";
                    if (questionsStack[b].answers.options.length)
                        for (f = 0; f < questionsStack[b].answers.options.length; f++) h = 30 < questionsStack[b].answers.options[f].length ? questionsStack[b].answers.options[f].substr(0, 30) + "..." : questionsStack[b].answers.options[f], option += '<option value="' + (f + 1) + '">' + h + "</option>"
            }
            d.html(c);
            a ? ($(".rule_option").show(), e.html(option)) : ($(".rule_option").hide(), e.html(""));
            getHideQuestion();
            getRulesQuestion();
            sfGetAnswers($("#sf_quest_list3"))
        }
        return !0
    },
    sfGetAnswers = function(a) {
        $("#hide_for_option").next().remove();
        $("#hide_for_option").remove();
        var b = $("#f_scale_data");
        b.html("");
        a = $(a).val();
        if ("0" != a) {
            var c = '<option value="0">- Select answer -</option>',
                d = questionsStack[a].sf_qtype;
            switch (d) {
                case "pick-one":
                case "pick-many":
                    if (questionsStack[a].answers.length)
                        for (var e = 0; e < questionsStack[a].answers.length; e++) d = 30 < questionsStack[a].answers[e].title.length ? questionsStack[a].answers[e].title.substr(0, 30) + "..." : questionsStack[a].answers[e].title, c += '<option value="' + (e + 1) + '">' + d + "</option>";
                    break;
                case "ranking":
                case "ranking-dragdrop":
                    var f = '<div class="control-group form-inline" id="hide_for_option"><label class="control-label">And for option:</label><div class="controls"><select id="sf_field_data_m" name="sf_field_data_m" style="width:250px;"></select></div></div><div style="clear:both"><br/></div>',
                        g = '<option value="0">- Select option -</option>';
                    if (questionsStack[a].answers.length)
                        for (e = 0; e < questionsStack[a].answers.length; e++) var h = 30 < questionsStack[a].answers[e].left.length ? questionsStack[a].answers[e].left.substr(0, 30) + "..." : questionsStack[a].answers[e].left,
                            g = g + ('<option value="' + (e + 1) + '">' + h + "</option>");
                    $(f).insertAfter($("#hide_for_question"));
                    $("#sf_field_data_m").html(g);
                    if (questionsStack[a].answers.length)
                        for (e = 0; e < questionsStack[a].answers.length; e++) d = 30 < questionsStack[a].answers[e].right.length ? questionsStack[a].answers[e].right.substr(0, 30) + "..." : questionsStack[a].answers[e].right, c += '<option value="' + (e + 1) + '">' + d + "</option>";
                    break;
                case "ranking-dropdown":
                case "likert-scale":
                    f = '<div class="control-group form-inline" id="hide_for_option"><label class="control-label">And for option:</label><div class="controls"><select id="sf_field_data_m" name="sf_field_data_m" style="width:250px;"></select></div></div><div style="clear:both"><br/></div>';
                    g = '<option value="0">- Select option -</option>';
                    if (questionsStack[a].answers.options.length)
                        for (e = 0; e < questionsStack[a].answers.options.length; e++) h = 30 < questionsStack[a].answers.options[e].length ? questionsStack[a].answers.options[e].substr(0, 30) + "..." : questionsStack[a].answers.options[e], g += '<option value="' + (e + 1) + '">' + h + "</option>";
                    $(f).insertAfter($("#hide_for_question"));
                    $("#sf_field_data_m").html(g);
                    if ("ranking-dropdown" == d) {
                        if (questionsStack[a].answers.ranks.length)
                            for (e = 0; e < questionsStack[a].answers.ranks.length; e++) d = 30 < questionsStack[a].answers.ranks[e].length ? questionsStack[a].answers.ranks[e].substr(0, 30) + "..." : questionsStack[a].answers.ranks[e], c += '<option value="' + (e + 1) + '">' + d + "</option>"
                    } else if ("likert-scale" == d && questionsStack[a].answers.scales.length)
                        for (e = 0; e < questionsStack[a].answers.scales.length; e++) d = 30 < questionsStack[a].answers.scales[e].length ? questionsStack[a].answers.scales[e].substr(0, 30) + "..." : questionsStack[a].answers.scales[e], c += '<option value="' + (e + 1) + '">' + d + "</option>"
            }
            b.html(c)
        }
        return !0
    },
    sfAddHideQuestion = function() {
        var a = $("#show_quest tbody");
        a || (a = '<tbody><tr id="title"><th colspan="4" class="title">Hide this question if:</th></tr></tbody>', $("#show_quest").append(a));
        var b = $(currQuestion).attr("id"),
            c = $("#sf_quest_list3").val(),
            d = $("#sf_field_data_m").val(),
            e = $("#f_scale_data").val();
        if ("0" == c || "0" == e) return alert("Select question and answer!"), !1;
        var f = questionsStack[b].sf_qtype;
        if (("ranking-dragdrop" == f || "ranking" == f || "ranking-dropdown" == f || "likert-scale" == f) && "0" == d) return alert("Select option please!"), !1;
        var g = $("#sf_quest_list3 option:selected").text(),
            h = $("#sf_field_data_m option:selected").text(),
            k = $("#f_scale_data option:selected").text(),
            g = '<tr><td class="hide_' + b + '">' + g + "</td><td>" + h + '</td><td class="ans_' + b + "_" + (e - 1) + '">' + k + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
        $(a).append(g);
        questionsStack[b].hides.push({
            qtype: f,
            question: c,
            option: d,
            answer: e
        });
        return !0
    },
    getHideQuestion = function() {
        if (currQuestion) {
            var a = $(currQuestion).attr("id");
            $("#show_quest tbody tr").not("#title").html("");
            var b = $("#show_quest tbody"),
                c = questionsStack[a].hides,
                d = '<tr id="title"><th colspan="4" class="title">Hide this question if:</th></tr>';
            if (currQuestion && c.length) {
                for (var e = 0; e < c.length; e++)
                    if (questionsStack[c[e].question]) {
                        var f = questionsStack[c[e].question].sf_qtitle,
                            f = 30 <= f.length ? f.substr(0, 30) + "..." : f;
                        switch (questionsStack[c[e].question].sf_qtype) {
                            case "pick-one":
                            case "pick-many":
                                var g = c[e].answer - 1;
                                scale_data = questionsStack[c[e].question].answers[g].title;
                                d = d + '<tr><td class="hide_' + c[e].question + '">' + f + '</td><td>&nbsp;</td><td class="answ_' + c[e].question + "_" + g + '">' + scale_data + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
                                break;
                            case "ranking":
                            case "ranking-dragdrop":
                                g = c[e].answer - 1;
                                d = d + '<tr><td class="hide_' + c[e].question + '">' + f + "</td><td>" + questionsStack[c[e].question].answers[g].left + '</td><td class="answ_' + c[e].question + "_" + g + '">' + questionsStack[c[e].question].answers[g].right + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
                                break;
                            case "ranking-dropdown":
                                var g = c[e].answer - 1,
                                    h = questionsStack[c[e].question].answers.options[g],
                                    d = d + '<tr><td class="hide_' + c[e].question + '">' + f + "</td><td>" + h + '</td><td class="answ_' + c[e].question + "_" + g + '">' + questionsStack[c[e].question].answers.ranks[g] + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
                                break;
                            case "likert-scale":
                                var g = c[e].answer - 1,
                                    k = questionsStack[c[e].question].answers.scales[g],
                                    h = questionsStack[c[e].question].answers.options[g],
                                    d = d + '<tr><td class="hide_' + c[e].question + '">' + f + "</td><td>" + h + '</td><td class="answ_' + c[e].question + "_" + g + '">' + k + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>'
                        }
                    } else questionsStack[a].hides.splice(e, 1);
                b.html(d)
            }
        }
        return !0
    },
    changeQuestionInHides = function() {
        var a = $(currQuestion).attr("id"),
            b = questionsStack[a].sf_qtitle;
        $(".hide_" + a).text(b);
        $(".rule_" + a).text(b)
    },
    changeAnswersInHides = function(a) {
        var b = $(currQuestion).attr("id");
        switch (questionsStack[b].sf_qtype) {
            case "pick-one":
            case "pick-many":
                var c = questionsStack[b].answers[a].title;
                break;
            case "ranking":
            case "ranking-dragdrop":
                c = questionsStack[b].answers[a].right;
                break;
            case "ranking-dropdown":
                c = questionsStack[b].answers.ranks[a];
                break;
            case "likert-scale":
                c = questionsStack[b].answers.scales[a]
        }
        $(".answ_" + b + "_" + a).text(c);
        $(".rule_answ_" + b + "_" + a).text(c)
    },
    removeAnswersInHides = function(a) {
        var b = $(currQuestion).attr("id");
        $(".answ_" + b + "_" + a).parent().remove()
    },
    removeHide = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent();
        a = $(c).index();
        questionsStack[b].hides.splice(a - 1, 1);
        $(c).fadeOut(300, function() {
            $(c).remove()
        })
    },
    sfAddQuestionRule = function() {
        var a = $("#qfld_tbl_rule tbody"),
            b = $(currQuestion).attr("id"),
            c = questionsStack[b].sf_qtype;
        if ("short-answer" == c || "boilerplate" == c) return !0;
        var c = $("#sf_field_list").val(),
            d = $("#sf_quest_list").val(),
            e = $("#new_priority").val(),
            f = $("#sf_option_list").val();
        if ("0" == c) return alert("Select answer please!"), !1;
        if ("0" == d) return alert("Select question please!"), !1;
        console.log("test");
        questionsStack[b].rules.push({
            question: d,
            answer: c,
            option: f,
            priority: e
        });
        var g = $("#sf_field_list option:selected").text(),
            h = $("#sf_quest_list option:selected").text(),
            k = $("#sf_option_list option:selected").text(),
            b = "<tr><td>&nbsp;</td>" + (f ? '<td class="rule_option_' + b + "_" + (f - 1) + '">' + k + "</td>" : "") + '<td class="rule_answ_' + b + "_" + (c - 1) + '">' + g + '</td><td class="rule_' + d + '">' + h + "</td><td>" + e + '</td><td><a class="remove hide-remove" href="#" onclick="removeRule(this);"><i class="fa fa-times"></i></a></td></tr>';
        $(a).append(b);
        $("#sf_option_list").val("");
        $("#sf_field_list").val("");
        $("#sf_quest_list").val("");
        $("#new_priority").val("")
    },
    getRulesQuestion = function() {
        var a = !1,
            b = $(currQuestion).attr("id");
        $("#qfld_tbl_rule tbody tr").not("tr:first").html("");
        var c = $("#qfld_tbl_rule tbody"),
            d = questionsStack[b].rules,
            e = '<tr><th align="center" width="2%">#</th><th width="25%" class="title rule_option" style="display:none;">Option</th><th width="25%" class="title">Answer</th><th width="25%" class="title">Question</th><th width="10%" class="title">priority </th><th width="auto"></th></tr>';
        if (currQuestion && d.length) {
            for (var f = 0; f < d.length; f++)
                if (questionsStack[d[f].question]) {
                    var g = questionsStack[d[f].question].sf_qtitle,
                        g = 30 <= g.length ? g.substr(0, 30) + "..." : g,
                        h = d[f].priority,
                        k = questionsStack[b].sf_qtype,
                        l = d[f].answer - 1,
                        m = d[f].option ? d[f].option - 1 : "-1";
                    if (questionsStack[b].answers[l]) {
                        switch (k) {
                            case "pick-one":
                            case "pick-many":
                                n = questionsStack[b].answers[l].title;
                                break;
                            case "ranking":
                            case "ranking-dragdrop":
                                var a = !0,
                                    n = questionsStack[b].answers[l].right,
                                    p = questionsStack[b].answers[m].left;
                                break;
                            case "ranking-dropdown":
                                a = !0;
                                n = questionsStack[b].answers.ranks[l];
                                p = questionsStack[b].answers.options[m];
                                break;
                            case "likert-scale":
                                a = !0, n = questionsStack[b].answers.scales[l], p = questionsStack[b].answers.options[m]
                        }
                        e = e + "<tr><td>&nbsp;</td>" + (a ? '<td class="rule_option_' + d[f].question + "_" + m + '">' + p + "</td>" : "") + '<td class="rule_answ_' + d[f].question + "_" + l + '">' + n + '</td><td class="rule_' + d[f].question + '">' + g + "</td><td>" + h + '</td><td><a class="remove hide-remove" href="#" onclick="removeRule(this);"><i class="fa fa-times"></i></a></td></tr>'
                    }
                }
            c.html(e);
            a && $(".rule_option").show()
        }
    },
    removeRule = function(a) {
        var b = $(currQuestion).attr("id"),
            c = $(a).parent().parent();
        a = $(c).index();
        questionsStack[b].rules.splice(a - 1, 1);
        $(c).fadeOut(300, function() {
            $(c).remove()
        })
    },
    sfSaveSurvey = function(a) {
        $(".viewport").css("opacity", .4);
        var b = document.getElementById("surveyForm"),
            b = "undefined" == typeof FormData ? new FormDataCompatibility(b) : new FormData(b),
            c = new XMLHttpRequest;
        c.open("POST", "index.php?option=com_surveyforce&task=survey.saveSurvey&tmpl=component&" + $('#token input').serialize());
        c.onreadystatechange = function() {
            if (4 == c.readyState && 200 == c.status) {
                js = c.responseText;
                if ("no login" == js) return parent.location.reload(), window.close(), !1;
                var a = $("#survey_id").val() ? $("#survey_id").val() : 0;
                var data = {
                    json: JSON.stringify(questionsStack)
                };
                data[encodeURIComponent($('#token input').attr('name'))] = 1;
                $.ajax({
                    url: "index.php?option=com_surveyforce&task=survey.saveQuestions&tmpl=component&surv_id=" + a,
                    method: "POST",
                    data: data,
                    dataType: 'json',
                    success: function(a) {
                        eval(js);
                        sfParseJSON(a);
                        $(".viewport").css("opacity", 1)
                    }
                })
            }
        };
        "undefined" == typeof FormData ? (b.setContentTypeHeader(c), c.send(b.buildBody())) : c.send(b);
        if (!firstSave || a) {
            a = 6E4 * parseInt($("#autosave").val());
            if (6E4 > a) return;
            "undefined" != typeof timer && clearInterval(timer);
            timer = setInterval("sfSaveSurvey(false)", a);
            firstSave = !0
        }
        return !1
    },
    sfParseJSON = function(a) {
        if (a) {
            for (var b in a.questions) questionsStack[b].id = parseInt(a.questions[b]);
            for (b in a.answers) {
                var c = questionsStack[b].sf_qtype;
                switch (c) {
                    case "pick-one":
                    case "pick-many":
                        if (a.answers[b].length)
                            for (var d = a.answers[b].length, e = 0; e < d; e++) questionsStack[b].answers[e].id = parseInt(a.answers[b][e]);
                        break;
                    case "ranking":
                    case "ranking-dragdrop":
                        if (a.answers[b].leftid.length)
                            for (d = a.answers[b].length, e = 0; e < d; e++) questionsStack[b].answers[e].leftid = parseInt(a.answers[b].leftid[e]), questionsStack[b].answers[e].rightid = parseInt(a.answers[b].rightid[e]);
                        break;
                    case "ranking-dropdown":
                    case "likert-scale":
                        if (a.answers[b].oid.length)
                            for (questionsStack[b].answers.oid = [], d = a.answers[b].oid.length, e = 0; e < d; e++) questionsStack[b].answers.oid.push(parseInt(a.answers[b].oid[e]));
                        c = "ranking-dropdown" == c ? "rid" : "sid";
                        if (a.answers[b][c].length)
                            for (questionsStack[b].answers[c] = [], d = a.answers[b][c].length, e = 0; e < d; e++) questionsStack[b].answers[c].push(parseInt(a.answers[b][c][e]))
                }
            }
        }
    };
window.FormDataCompatibility = function() {
    function a(a) {
        this.fields = {};
        this.boundary = this.generateBoundary();
        this.contentType = "multipart/form-data; boundary=" + this.boundary;
        this.CRLF = "\r\n";
        if ("undefined" !== typeof a)
            for (var c = 0; c < a.elements.length; c++) {
                var d = a.elements[c],
                    e = null !== d.name && "" !== d.name ? d.name : this.getElementNameByIndex(c);
                this.append(e, d)
            }
    }
    a.prototype.getElementNameByIndex = function(a) {
        return "___form_element__" + a
    };
    a.prototype.append = function(a, c) {
        return this.fields[a] = c
    };
    a.prototype.setContentTypeHeader = function(a) {
        return a.setRequestHeader("Content-Type", this.contentType)
    };
    a.prototype.getContentType = function() {
        return this.contentType
    };
    a.prototype.generateBoundary = function() {
        return "AJAX--------------" + (new Date).getTime()
    };
    a.prototype.buildBody = function() {
        var a, c, d, e;
        c = [];
        e = this.fields;
        for (a in e) d = e[a], c.push(this.buildPart(a, d));
        a = "--" + this.boundary + this.CRLF;
        a += c.join("--" + this.boundary + this.CRLF);
        return a += "--" + this.boundary + "--" + this.CRLF
    };
    a.prototype.buildPart = function(a, c) {
        var d;
        "string" === typeof c ? (d = 'Content-Disposition: form-data; name="' + a + '"' + this.CRLF, d += "Content-Type: text/plain; charset=utf-8" + this.CRLF + this.CRLF, d += unescape(encodeURIComponent(c)) + this.CRLF) : typeof c === typeof File ? (d = 'Content-Disposition: form-data; name="' + a + '"; filename="' + c.fileName + '"' + this.CRLF, d += "Content-Type: " + c.type + this.CRLF + this.CRLF, d += c.getAsBinary() + this.CRLF) : typeof c === typeof HTMLInputElement && "file" != c.type && (d = 'Content-Disposition: form-data; name="' + a + '"' + this.CRLF, d += "Content-Type: text/plain; charset=utf-8" + this.CRLF + this.CRLF, d += unescape(encodeURIComponent(c.value)) + this.CRLF);
        return d
    };
    return a
}();