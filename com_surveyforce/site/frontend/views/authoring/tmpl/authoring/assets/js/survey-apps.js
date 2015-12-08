var $ = jQuery;
var currQuestion;
var fileInterval = null;
var file = new Object();
var firstSave = false;

var BULKS = [{"type": "vertical", "values": [{"fr": "Under 18", "en": "Under 18"}, {"fr": "18-24", "en": "18-24"}, {"fr": "25-34", "en": "25-34"}, {"fr": "35-44", "en": "35-44"}, {"fr": "45-54", "en": "45-54"}, {"fr": "55-64", "en": "55-64"}, {"fr": "65 or Above", "en": "65 or Above"}, {"fr": "Prefer Not to Answer", "en": "Prefer Not to Answer"}], "name": "Age"}, {"type": "vertical", "values": [{"fr": "Employed Full-Time", "en": "Employed Full-Time"}, {"fr": "Employed Part-Time", "en": "Employed Part-Time"}, {"fr": "Self-employed", "en": "Self-employed"}, {"fr": "Not employed, but looking for work", "en": "Not employed, but looking for work"}, {"fr": "Not employed and not looking for work", "en": "Not employed and not looking for work"}, {"fr": "Homemaker", "en": "Homemaker"}, {"fr": "Retired", "en": "Retired"}, {"fr": "Student", "en": "Student"}, {"fr": "Prefer Not to Answer", "en": "Prefer Not to Answer"}], "name": "Employment"}, {"type": "vertical", "values": [{"fr": "Under $20,000", "en": "Under $20,000"}, {"fr": "$20,000 - $30,000", "en": "$20,000 - $30,000"}, {"fr": "$30,000 - $40,000", "en": "$30,000 - $40,000"}, {"fr": "$40,000 - $50,000", "en": "$40,000 - $50,000"}, {"fr": "$50,000 - $75,000", "en": "$50,000 - $75,000"}, {"fr": "$75,000 - $100,000", "en": "$75,000 - $100,000"}, {"fr": "$100,000 - $150,000", "en": "$100,000 - $150,000"}, {"fr": "$150,000 or more", "en": "$150,000 or more"}, {"fr": "Prefer Not to Answer", "en": "Prefer Not to Answer"}], "name": "Income Level"}, {"type": "horizontal", "values": [{"fr": "Some High School", "en": "Some High School"}, {"fr": "High School Graduate or Equivalent", "en": "High School Graduate or Equivalent"}, {"fr": "Trade or Vocational Degree", "en": "Trade or Vocational Degree"}, {"fr": "Some College", "en": "Some College"}, {"fr": "Associate Degree", "en": "Associate Degree"}, {"fr": "Bachelor's Degree", "en": "Bachelor's Degree"}, {"fr": "Graduate of Professional Degree", "en": "Graduate of Professional Degree"}, {"fr": "Prefer Not to Answer", "en": "Prefer Not to Answer"}], "name": "Education"}, {"type": "vertical", "values": [{"fr": "Single, Never Married", "en": "Single, Never Married"}, {"fr": "Married", "en": "Married"}, {"fr": "Living with Partner", "en": "Living with Partner"}, {"fr": "Separated", "en": "Separated"}, {"fr": "Divorced", "en": "Divorced"}, {"fr": "Widowed", "en": "Widowed"}, {"fr": "Prefer Not to Answer", "en": "Prefer Not to Answer"}], "name": "Marital Status"}, {"type": "vertical", "values": [{"fr": "White / Caucasian", "en": "White / Caucasian"}, {"fr": "Spanish / Hispanic / Latino", "en": "Spanish / Hispanic / Latino"}, {"fr": "Black / African American", "en": "Black / African American"}, {"fr": "Asian", "en": "Asian"}, {"fr": "Pacific Islander", "en": "Pacific Islander"}, {"fr": "Native American", "en": "Native American"}, {"fr": "Other", "en": "Other"}, {"fr": "Prefer Not to Answer", "en": "Prefer Not to Answer"}], "name": "Race"}, {"type": "vertical", "values": [{"fr": "janvier", "en": "January"}, {"fr": "f\u00e9vrier", "en": "February"}, {"fr": "mars", "en": "March"}, {"fr": "avril", "en": "April"}, {"fr": "mai", "en": "May"}, {"fr": "juin", "en": "June"}, {"fr": "juillet", "en": "July"}, {"fr": "ao\u00fbt", "en": "August"}, {"fr": "septembre", "en": "September"}, {"fr": "octobre", "en": "October"}, {"fr": "novembre", "en": "November"}, {"fr": "d\u00e9cembre", "en": "December"}], "name": "Months"}, {"type": "vertical", "values": [{"fr": "lundi", "en": "Monday"}, {"fr": "mardi", "en": "Tuesday"}, {"fr": "mercredi", "en": "Wednesday"}, {"fr": "jeudi", "en": "Thursday"}, {"fr": "vendredi", "en": "Friday"}, {"fr": "samedi", "en": "Saturday"}, {"fr": "dimanche", "en": "Sunday"}], "name": "Days"}, {"type": "vertical", "values": [{"fr": "Alberta", "en": "Alberta"}, {"fr": "Colombie-Britannique", "en": "British Columbia"}, {"fr": "Manitoba", "en": "Manitoba"}, {"fr": "Nouveau-Brunswick", "en": "New Brunswick"}, {"fr": "Terre-Neuve-et-Labrador", "en": "Newfoundland and Labrador"}, {"fr": "Territoires du Nord-Ouest", "en": "Northwest Territories"}, {"fr": "Nouvelle-\u00c9cosse", "en": "Nova Scotia"}, {"fr": "Nunavut", "en": "Nunavut"}, {"fr": "Ontario", "en": "Ontario"}, {"fr": "l'\u00eele du Prince-\u00c9douard", "en": "Prince Edward Island"}, {"fr": "Qu\u00e9bec", "en": "Quebec"}, {"fr": "Saskatchewan", "en": "Saskatchewan"}, {"fr": "Yukon", "en": "Yukon"}], "name": "Canadian Provinces"}, {"type": "vertical", "values": [{"fr": "Alabama", "en": "Alabama"}, {"fr": "Alaska", "en": "Alaska"}, {"fr": "Arizona", "en": "Arizona"}, {"fr": "Arkansas", "en": "Arkansas"}, {"fr": "Californie", "en": "California"}, {"fr": "Colorado", "en": "Colorado"}, {"fr": "Connecticut", "en": "Connecticut"}, {"fr": "Delaware", "en": "Delaware"}, {"fr": "Floride", "en": "Florida"}, {"fr": "G\u00e9orgie", "en": "Georgia"}, {"fr": "Hawa\u00ef", "en": "Hawaii"}, {"fr": "Idaho", "en": "Idaho"}, {"fr": "Illinois", "en": "Illinois"}, {"fr": "Indiana", "en": "Indiana"}, {"fr": "Iowa", "en": "Iowa"}, {"fr": "Kansas", "en": "Kansas"}, {"fr": "Kentucky", "en": "Kentucky"}, {"fr": "Louisiane", "en": "Louisiana"}, {"fr": "Maine", "en": "Maine"}, {"fr": "Mariland", "en": "Maryland"}, {"fr": "Massachusetts", "en": "Massachusetts"}, {"fr": "Michigan", "en": "Michigan"}, {"fr": "Minnesota", "en": "Minnesota"}, {"fr": "Mississippi", "en": "Mississippi"}, {"fr": "Missouri", "en": "Missouri"}, {"fr": "Montana", "en": "Montana"}, {"fr": "Nebraska", "en": "Nebraska"}, {"fr": "Nevada", "en": "Nevada"}, {"fr": "New Hampshire", "en": "New Hampshire"}, {"fr": "New Jersey", "en": "New Jersey"}, {"fr": "Nouveau-Mexique", "en": "New Mexico"}, {"fr": "New-York", "en": "New York"}, {"fr": "Caroline du Nord", "en": "North Carolina"}, {"fr": "Dakota du Nord", "en": "North Dakota"}, {"fr": "Ohio", "en": "Ohio"}, {"fr": "Oklahoma", "en": "Oklahoma"}, {"fr": "Oregon", "en": "Oregon"}, {"fr": "Pennsylvanie", "en": "Pennsylvania"}, {"fr": "Rhode Island", "en": "Rhode Island"}, {"fr": "Caroline du Sud", "en": "South Carolina"}, {"fr": "Dakota du Sud", "en": "South Dakota"}, {"fr": "Tennessee", "en": "Tennessee"}, {"fr": "Texas", "en": "Texas"}, {"fr": "Utah", "en": "Utah"}, {"fr": "Vermont", "en": "Vermont"}, {"fr": "Virginie", "en": "Virginia"}, {"fr": "Washington", "en": "Washington"}, {"fr": "Virginie Occidentale", "en": "West Virginia"}, {"fr": "Wisconsin", "en": "Wisconsin"}, {"fr": "Wyoming", "en": "Wyoming"}], "name": "US States"}, {"type": "vertical", "values": [{"fr": "Afghanistan", "en": "Afghanistan"}, {"fr": "Albanie", "en": "Albania"}, {"fr": "Alg\u00e9rie", "en": "Algeria"}, {"fr": "Andorre", "en": "Andorra"}, {"fr": "Angola", "en": "Angola"}, {"fr": "Antarctique", "en": "Antarctica"}, {"fr": "Antigua and Barbuda", "en": "Antigua and Barbuda"}, {"fr": "Argentine", "en": "Argentina"}, {"fr": "Arm\u00e9nie", "en": "Armenia"}, {"fr": "Australie", "en": "Australia"}, {"fr": "Autriche", "en": "Austria"}, {"fr": "Azerba\u00efdjan", "en": "Azerbaijan"}, {"fr": "Bahamas", "en": "Bahamas"}, {"fr": "Bahre\u00efn", "en": "Bahrain"}, {"fr": "Bangladesh", "en": "Bangladesh"}, {"fr": "La Barbade", "en": "Barbados"}, {"fr": "Bi\u00e9lorussie", "en": "Belarus"}, {"fr": "Belgique", "en": "Belgium"}, {"fr": "B\u00e9lize", "en": "Belize"}, {"fr": "B\u00e9nin", "en": "Benin"}, {"fr": "Bermudes", "en": "Bermuda"}, {"fr": "Boutan", "en": "Bhutan"}, {"fr": "Bolivie", "en": "Bolivia"}, {"fr": "Bosnie-Herz\u00e9govine", "en": "Bosnia and Herzegovina"}, {"fr": "Botswana", "en": "Botswana"}, {"fr": "Br\u00e9sil", "en": "Brazil"}, {"fr": "Brunei", "en": "Brunei"}, {"fr": "Bulgarie", "en": "Bulgaria"}, {"fr": "Burkina Faso", "en": "Burkina Faso"}, {"fr": "Burma", "en": "Burma"}, {"fr": "Burundi", "en": "Burundi"}, {"fr": "Cambodge", "en": "Cambodia"}, {"fr": "Cameroun", "en": "Cameroon"}, {"fr": "Canada", "en": "Canada"}, {"fr": "Cap-Vert", "en": "Cape Verde"}, {"fr": "R\u00e9publique Centrafricaine", "en": "Central African Republic"}, {"fr": "Tchad", "en": "Chad"}, {"fr": "Chili", "en": "Chile"}, {"fr": "Chine", "en": "China"}, {"fr": "Colombie", "en": "Colombia"}, {"fr": "Comores", "en": "Comoros"}, {"fr": "Congo, Democratic Republic", "en": "Congo, Democratic Republic"}, {"fr": "Congo, Republic of the", "en": "Congo, Republic of the"}, {"fr": "Costa Rica", "en": "Costa Rica"}, {"fr": "Cote d'Ivoire", "en": "Cote d'Ivoire"}, {"fr": "Croatie", "en": "Croatia"}, {"fr": "Cuba", "en": "Cuba"}, {"fr": "Chypre", "en": "Cyprus"}, {"fr": "R\u00e9publique tch\u00e8que", "en": "Czech Republic"}, {"fr": "Danemark", "en": "Denmark"}, {"fr": "Djibouti", "en": "Djibouti"}, {"fr": "Dominique", "en": "Dominica"}, {"fr": "R\u00e9publique Dominicaine", "en": "Dominican Republic"}, {"fr": "Timor Oriental", "en": "East Timor"}, {"fr": "\u00c9quateur", "en": "Ecuador"}, {"fr": "\u00c9gypte", "en": "Egypt"}, {"fr": "Salvador", "en": "El Salvador"}, {"fr": "Guin\u00e9e \u00c9quatoriale", "en": "Equatorial Guinea"}, {"fr": "\u00c9rythr\u00e9e", "en": "Eritrea"}, {"fr": "Estonie", "en": "Estonia"}, {"fr": "Ethiopie", "en": "Ethiopia"}, {"fr": "Fidji", "en": "Fiji"}, {"fr": "Finlande", "en": "Finland"}, {"fr": "France", "en": "France"}, {"fr": "Gabon", "en": "Gabon"}, {"fr": "Gambie", "en": "Gambia"}, {"fr": "G\u00e9orgie", "en": "Georgia"}, {"fr": "Allemagne", "en": "Germany"}, {"fr": "Ghana", "en": "Ghana"}, {"fr": "Gr\u00e8ce", "en": "Greece"}, {"fr": "Groenland", "en": "Greenland"}, {"fr": "Grenade", "en": "Grenada"}, {"fr": "Guatemala", "en": "Guatemala"}, {"fr": "Guin\u00e9e", "en": "Guinea"}, {"fr": "Guin\u00e9e-Bissau", "en": "Guinea-Bissau"}, {"fr": "Guyanne", "en": "Guyana"}, {"fr": "Ha\u00efti", "en": "Haiti"}, {"fr": "Honduras", "en": "Honduras"}, {"fr": "Hong-Kong", "en": "Hong Kong"}, {"fr": "Hongrie", "en": "Hungary"}, {"fr": "Islande", "en": "Iceland"}, {"fr": "Inde", "en": "India"}, {"fr": "Indon\u00e9sie", "en": "Indonesia"}, {"fr": "Iran", "en": "Iran"}, {"fr": "Irak", "en": "Iraq"}, {"fr": "Irlande", "en": "Ireland"}, {"fr": "Isra\u00ebl", "en": "Israel"}, {"fr": "Italie", "en": "Italy"}, {"fr": "Jama\u00efque", "en": "Jamaica"}, {"fr": "Japon", "en": "Japan"}, {"fr": "Jordanie", "en": "Jordan"}, {"fr": "Kazakhstan", "en": "Kazakhstan"}, {"fr": "Kenya", "en": "Kenya"}, {"fr": "Kiribati", "en": "Kiribati"}, {"fr": "Korea, North", "en": "Korea, North"}, {"fr": "Korea, South", "en": "Korea, South"}, {"fr": "Kowe\u00eft", "en": "Kuwait"}, {"fr": "Kirghizistan", "en": "Kyrgyzstan"}, {"fr": "Laos", "en": "Laos"}, {"fr": "Lettonie", "en": "Latvia"}, {"fr": "Liban", "en": "Lebanon"}, {"fr": "Lesotho", "en": "Lesotho"}, {"fr": "Lib\u00e9ria", "en": "Liberia"}, {"fr": "Libya", "en": "Libya"}, {"fr": "Liechtenstein", "en": "Liechtenstein"}, {"fr": "Lituanie", "en": "Lithuania"}, {"fr": "Luxembourg", "en": "Luxembourg"}, {"fr": "Macedonia", "en": "Macedonia"}, {"fr": "Madagascar", "en": "Madagascar"}, {"fr": "Malawi", "en": "Malawi"}, {"fr": "Malaisie", "en": "Malaysia"}, {"fr": "Maldives", "en": "Maldives"}, {"fr": "Mali", "en": "Mali"}, {"fr": "Malte", "en": "Malta"}, {"fr": "Iles Marshall", "en": "Marshall Islands"}, {"fr": "Mauritanie", "en": "Mauritania"}, {"fr": "\u00cele Maurice", "en": "Mauritius"}, {"fr": "Mexique", "en": "Mexico"}, {"fr": "Micron\u00e9sie", "en": "Micronesia"}, {"fr": "Moldova", "en": "Moldova"}, {"fr": "Mongolie", "en": "Mongolia"}, {"fr": "Maroc", "en": "Morocco"}, {"fr": "Monaco", "en": "Monaco"}, {"fr": "Montenegro", "en": "Montenegro"}, {"fr": "Mozambique", "en": "Mozambique"}, {"fr": "Namibie", "en": "Namibia"}, {"fr": "Nauru", "en": "Nauru"}, {"fr": "N\u00e9pal", "en": "Nepal"}, {"fr": "Pays-Bas", "en": "Netherlands"}, {"fr": "Nouvelle-Z\u00e9lande", "en": "New Zealand"}, {"fr": "Nicaragua", "en": "Nicaragua"}, {"fr": "Niger", "en": "Niger"}, {"fr": "Nig\u00e9ria", "en": "Nigeria"}, {"fr": "Norv\u00e8ge", "en": "Norway"}, {"fr": "Oman", "en": "Oman"}, {"fr": "Pakistan", "en": "Pakistan"}, {"fr": "Panama", "en": "Panama"}, {"fr": "Papouasie-Nouvelle-Guin\u00e9e", "en": "Papua New Guinea"}, {"fr": "Paraguay", "en": "Paraguay"}, {"fr": "P\u00e9rou", "en": "Peru"}, {"fr": "Philippines", "en": "Philippines"}, {"fr": "Pologne", "en": "Poland"}, {"fr": "Portugal", "en": "Portugal"}, {"fr": "Qatar", "en": "Qatar"}, {"fr": "Roumanie", "en": "Romania"}, {"fr": "Russia", "en": "Russia"}, {"fr": "Rwanda", "en": "Rwanda"}, {"fr": "Samoa", "en": "Samoa"}, {"fr": "Saint-Marin", "en": "San Marino"}, {"fr": "Sao Tome", "en": "Sao Tome"}, {"fr": "Arabie Saoudite", "en": "Saudi Arabia"}, {"fr": "S\u00e9n\u00e9gal", "en": "Senegal"}, {"fr": "Serbia", "en": "Serbia"}, {"fr": "Seychelles", "en": "Seychelles"}, {"fr": "Sierra Leone", "en": "Sierra Leone"}, {"fr": "Singapour", "en": "Singapore"}, {"fr": "Slovaquie", "en": "Slovakia"}, {"fr": "Slov\u00e9nie", "en": "Slovenia"}, {"fr": "Salomon, \u00celes", "en": "Solomon Islands"}, {"fr": "Somalie", "en": "Somalia"}, {"fr": "Afrique du Sud", "en": "South Africa"}, {"fr": "Espagne", "en": "Spain"}, {"fr": "Sri Lanka", "en": "Sri Lanka"}, {"fr": "Soudan", "en": "Sudan"}, {"fr": "Surinam", "en": "Suriname"}, {"fr": "Swaziland", "en": "Swaziland"}, {"fr": "Su\u00e8de", "en": "Sweden"}, {"fr": "Suisse", "en": "Switzerland"}, {"fr": "Syria", "en": "Syria"}, {"fr": "Taiwan", "en": "Taiwan"}, {"fr": "Tadjikistan", "en": "Tajikistan"}, {"fr": "Tanzania", "en": "Tanzania"}, {"fr": "Tha\u00eflande", "en": "Thailand"}, {"fr": "Togo", "en": "Togo"}, {"fr": "Tonga", "en": "Tonga"}, {"fr": "Trinidad and Tobago", "en": "Trinidad and Tobago"}, {"fr": "Tunisie", "en": "Tunisia"}, {"fr": "Turquie", "en": "Turkey"}, {"fr": "Turkm\u00e9nistan", "en": "Turkmenistan"}, {"fr": "Ouganda", "en": "Uganda"}, {"fr": "Ukraine", "en": "Ukraine"}, {"fr": "\u00c9mirats Arabes Unis", "en": "United Arab Emirates"}, {"fr": "United Kingdom", "en": "United Kingdom"}, {"fr": "United States", "en": "United States"}, {"fr": "Uruguay", "en": "Uruguay"}, {"fr": "Ouzb\u00e9kistan", "en": "Uzbekistan"}, {"fr": "Vanuatu", "en": "Vanuatu"}, {"fr": "Venezuela", "en": "Venezuela"}, {"fr": "Vietnam", "en": "Vietnam"}, {"fr": "Y\u00e9men", "en": "Yemen"}, {"fr": "Zambie", "en": "Zambia"}, {"fr": "Zimbabwe", "en": "Zimbabwe"}], "name": "Countries"}, {"type": "vertical", "values": [{"fr": "Africa", "en": "Africa"}, {"fr": "Antarctique", "en": "Antarctica"}, {"fr": "Asia", "en": "Asia"}, {"fr": "Australie", "en": "Australia"}, {"fr": "Europe", "en": "Europe"}, {"fr": "North America", "en": "North America"}, {"fr": "South America", "en": "South America"}], "name": "Continents"}, {"type": "vertical horizontal", "values": [{"fr": "Everyday", "en": "Everyday"}, {"fr": "Once a week", "en": "Once a week"}, {"fr": "2 to 3 times a month", "en": "2 to 3 times a month"}, {"fr": "Once a month", "en": "Once a month"}, {"fr": "Less than once a month", "en": "Less than once a month"}], "name": "How Often?"}, {"type": "vertical horizontal", "values": [{"fr": "Jamais", "en": "Never"}, {"fr": "Rarely", "en": "Rarely"}, {"fr": "Sometimes", "en": "Sometimes"}, {"fr": "Often", "en": "Often"}, {"fr": "Always", "en": "Always"}], "name": "Frequency"}, {"type": "vertical horizontal", "values": [{"fr": "Less than a month", "en": "Less than a month"}, {"fr": "1-6 months", "en": "1-6 months"}, {"fr": "1-3 years", "en": "1-3 years"}, {"fr": "Over 3 Years", "en": "Over 3 Years"}, {"fr": "Never used", "en": "Never used"}], "name": "How Long?"}, {"type": "vertical", "values": [{"fr": "Very Satisfied", "en": "Very Satisfied"}, {"fr": "Satisfied", "en": "Satisfied"}, {"fr": "Neutral", "en": "Neutral"}, {"fr": "Unsatisfied", "en": "Unsatisfied"}, {"fr": "Very Unsatisfied", "en": "Very Unsatisfied"}, {"fr": "Non applicable", "en": "N/A"}], "name": "Satisfaction"}, {"type": "horizontal", "values": [{"fr": "Very Unsatisfied", "en": "Very Unsatisfied"}, {"fr": "Unsatisfied", "en": "Unsatisfied"}, {"fr": "Neutral", "en": "Neutral"}, {"fr": "Satisfied", "en": "Satisfied"}, {"fr": "Very Satisfied", "en": "Very Satisfied"}], "name": "Satisfaction"}, {"type": "vertical", "values": [{"fr": "Very Important", "en": "Very Important"}, {"fr": "Important", "en": "Important"}, {"fr": "Neutral", "en": "Neutral"}, {"fr": "Somewhat Important", "en": "Somewhat Important"}, {"fr": "Not at all Important", "en": "Not at all Important"}, {"fr": "Non applicable", "en": "N/A"}], "name": "Importance"}, {"type": "horizontal", "values": [{"fr": "Not at all Important", "en": "Not at all Important"}, {"fr": "Somewhat Important", "en": "Somewhat Important"}, {"fr": "Neutral", "en": "Neutral"}, {"fr": "Important", "en": "Important"}, {"fr": "Very Important", "en": "Very Important"}], "name": "Importance"}, {"type": "vertical", "values": [{"fr": "Very Happy", "en": "Very Happy"}, {"fr": "Happy", "en": "Happy"}, {"fr": "Indifferent", "en": "Indifferent"}, {"fr": "Unhappy", "en": "Unhappy"}, {"fr": "Very Unhappy", "en": "Very Unhappy"}, {"fr": "Non applicable", "en": "N/A"}], "name": "Happiness"}, {"type": "horizontal", "values": [{"fr": "Very Unhappy", "en": "Very Unhappy"}, {"fr": "Unhappy", "en": "Unhappy"}, {"fr": "Indifferent", "en": "Indifferent"}, {"fr": "Happy", "en": "Happy"}, {"fr": "Very Happy", "en": "Very Happy"}], "name": "Happiness"}, {"type": "vertical", "values": [{"fr": "Strongly Agree", "en": "Strongly Agree"}, {"fr": "Agree", "en": "Agree"}, {"fr": "Neutral", "en": "Neutral"}, {"fr": "Disagree", "en": "Disagree"}, {"fr": "Strongly Disagree", "en": "Strongly Disagree"}, {"fr": "Non applicable", "en": "N/A"}], "name": "Agreement"}, {"type": "horizontal", "values": [{"fr": "Strongly Disagree", "en": "Strongly Disagree"}, {"fr": "Disagree", "en": "Disagree"}, {"fr": "Neutral", "en": "Neutral"}, {"fr": "Agree", "en": "Agree"}, {"fr": "Strongly Agree", "en": "Strongly Agree"}], "name": "Agreement"}, {"type": "vertical", "values": [{"fr": "Much Better", "en": "Much Better"}, {"fr": "Somewhat Better", "en": "Somewhat Better"}, {"fr": "About the Same", "en": "About the Same"}, {"fr": "Somewhat Worse", "en": "Somewhat Worse"}, {"fr": "Much Worse", "en": "Much Worse"}, {"fr": "Don't Know", "en": "Don't Know"}], "name": "Comparison"}, {"type": "horizontal", "values": [{"fr": "Much Worse", "en": "Much Worse"}, {"fr": "Somewhat Worse", "en": "Somewhat Worse"}, {"fr": "About the Same", "en": "About the Same"}, {"fr": "Somewhat Better", "en": "Somewhat Better"}, {"fr": "Much Better", "en": "Much Better"}], "name": "Comparison"}, {"type": "vertical", "values": [{"fr": "Definitely", "en": "Definitely"}, {"fr": "Probably", "en": "Probably"}, {"fr": "Not Sure", "en": "Not Sure"}, {"fr": "Probably Not", "en": "Probably Not"}, {"fr": "Definitely Not", "en": "Definitely Not"}], "name": "Probability"}, {"type": "horizontal", "values": [{"fr": "Definitely Not", "en": "Definitely Not"}, {"fr": "Probably Not", "en": "Probably Not"}, {"fr": "Not Sure", "en": "Not Sure"}, {"fr": "Probably", "en": "Probably"}, {"fr": "Definitely", "en": "Definitely"}], "name": "Probability"}, {"type": "vertical horizontal", "values": ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"], "name": "10 Scale"}, {"type": "vertical", "values": ["Male", "Female", "Prefer Not to Answer"], "name": "Gender"}, {"type": "vertical horizontal", "values": [{"en": "2013"}, {"en": "2012"}, {"en": "2011"}, {"en": "2010"}, {"en": "2009"}, {"en": "2008"}, {"en": "2007"}, {"en": "2006"}, {"en": "2005"}, {"en": "2004"}, {"en": "2003"}, {"en": "2002"}, {"en": "2001"}, {"en": "2000"}, {"en": "1999"}, {"en": "1998"}, {"en": "1997"}, {"en": "1996"}, {"en": "1995"}, {"en": "1994"}, {"en": "1993"}, {"en": "1992"}, {"en": "1991"}, {"en": "1990"}, {"en": "1989"}, {"en": "1988"}, {"en": "1987"}, {"en": "1986"}, {"en": "1985"}, {"en": "1984"}, {"en": "1983"}, {"en": "1982"}, {"en": "1981"}, {"en": "1980"}, {"en": "1979"}, {"en": "1978"}, {"en": "1977"}, {"en": "1976"}, {"en": "1975"}, {"en": "1974"}, {"en": "1973"}, {"en": "1972"}, {"en": "1971"}, {"en": "1970"}, {"en": "1969"}, {"en": "1968"}, {"en": "1967"}, {"en": "1966"}, {"en": "1965"}, {"en": "1964"}, {"en": "1963"}, {"en": "1962"}, {"en": "1961"}, {"en": "1960"}, {"en": "1959"}, {"en": "1958"}, {"en": "1957"}, {"en": "1956"}, {"en": "1955"}, {"en": "1954"}, {"en": "1953"}, {"en": "1952"}, {"en": "1951"}, {"en": "1950"}, {"en": "1949"}, {"en": "1948"}, {"en": "1947"}, {"en": "1946"}, {"en": "1945"}, {"en": "1944"}, {"en": "1943"}, {"en": "1942"}, {"en": "1941"}, {"en": "1940"}, {"en": "1939"}, {"en": "1938"}, {"en": "1937"}, {"en": "1936"}, {"en": "1935"}, {"en": "1934"}, {"en": "1933"}, {"en": "1932"}, {"en": "1931"}, {"en": "1930"}, {"en": "1929"}, {"en": "1928"}, {"en": "1927"}, {"en": "1926"}, {"en": "1925"}, {"en": "1924"}, {"en": "1923"}, {"en": "1922"}, {"en": "1921"}, {"en": "1920"}, {"en": "1919"}, {"en": "1918"}, {"en": "1917"}, {"en": "1916"}, {"en": "1915"}, {"en": "1914"}, {"en": "1913"}, {"en": "1912"}, {"en": "1911"}, {"en": "1910"}, {"en": "1909"}, {"en": "1908"}, {"en": "1907"}, {"en": "1906"}, {"en": "1905"}, {"en": "1904"}, {"en": "1903"}, {"en": "1902"}, {"en": "1901"}, {"en": "1900"}], "name": "Years"}];

$(document).ready(function(){
	
	sf_createQuestions();
	if ($.fn.selectpicker) {
   		$(".selectpicker").selectpicker()
	}

	$("#CKeditor").text("");
	CKEDITOR.replace('CKeditor');

	$("#sf_date").datepicker({
		showOn: "button",
		buttonText: COM_SURVEYFORCE_SELECT_DATE,
		showOptions: { direction: "up" }
	});

	$( "li.tool" ).tooltip({
		hide: {
			effect: "explode",
			delay: 250
		}
	});
	$( "#basicquestions li" ).draggable({
		appendTo: "body",
		helper: "clone",
		start: function(event, ui) {
			dropped = false;
			ui.helper.addClass("tools-move");
		}
	});

	$( "#survey-questions" + currPage ).droppable({
		drop: function( event, ui ) {
			qtype = $(ui.helper).attr("field-type");
			sfAddQuestion(qtype);
		}
	}).sortable({
		axis: "y",
		placeholder: "ui-state-highlight",
		cursor: "move",
		stop: function( event, ui )
		{
			sf_SortQuestions(event, ui);
		}
	});

	$(".viewport .tabs").sortable({
		placeholder: "ui-state-highlight",
		cursor: "move",
		stop: function( event, ui ) {
			sfOrderingPages(event, ui);
			sf_SortQuestions(event, ui);
		}
	});

});

var sfPublishSurvey = function(surv_id, base_url)
{
	window.location.href = base_url + 'index.php?option=com_surveyforce&view=survey&id=' + surv_id;
	return false;
}

var sf_SortQuestions = function(event, ui)
{
	var ordering = 0;
	var pageBreakId = new Array();
	var pages = $(".page");

	$(pages).each(function(){
		var quests = $(this).find("li.field");
		$(quests).each(function(){
			var id = $(this).attr("id");
			if(questionsStack[id])
				questionsStack[id].questOrdering = ordering;

			ordering++;
		});

		for(var liid in questionsStack){
			if(questionsStack[liid].sf_qtype == 'page-break'){
				if(!sf_inArray(pageBreakId, liid)){
					questionsStack[liid].questOrdering = ordering;
					ordering++;
					pageBreakId.push(liid);
					break;
				}
			}
		}
	});
}

var sf_inArray = function(array, value){
	if(!array.length) return false;

	for(var n = 0; n < array.length; n++){
		if(array[n] == value){
			return true;
		}
	}

	return false;
}

var sf_createQuestions = function(){
	
	if(questionsStack){
		$(".placeholder").remove();
		for(var liID in questionsStack){
			var qhtml = '<li class="field" name="' + questionsStack[liID].sf_qtype + '" style="" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">' + questionsStack[liID].sf_qtitle + '</h3><div class="description"></div>{ANSWERS}</li>';

			switch(questionsStack[liID].sf_qtype){
				case 'section-separator':
					var sections = questionsStack[liID].sections;
					if(sections.length){
						for(var k = 0; k < sections.length; k++){

							qid = sections[k];
							for(var prop in questionsStack){
								if(questionsStack[prop].id == qid){
									var systemid = prop;
									questionsStack[liID].sections[k] = systemid;
								}
							}

						}
					}
					qhtml = qhtml.replace('{ANSWERS}', '');
				break;
				case 'pick-one':
				case 'pick-many':

					var typeHtml = (questionsStack[liID].sf_qtype == 'pick-one') ? 'radio' : 'checkbox';
					var nameID = sfGenerateID();
					if(questionsStack[liID].answers.length){

						choices = '<ul class="choices" data-id="' + nameID + '">';
						for(var n = 0; n < questionsStack[liID].answers.length; n++){
							choices += '<li class="choice"><label><label class="clean-input-wrap"><input type="' + typeHtml + '" name="' + nameID + '"><span class="clean-input"></span></label><span class="choice-value">' + questionsStack[liID].answers[n].title + '</span></label></li>';
						}

						if(questionsStack[liID].answers[0]['other_option']){
							choices += '<li class="choice other"><label><label class="clean-input-wrap"><input type="radio" name="'+nameID+'"><span class="clean-input"></span></label> <span class="choice-value">' + questionsStack[liID].answers[0]['other_option_text'] + '</span><input type="text" "="" class="text dummy"></label></li>';
						}

						choices += '</ul>';
						qhtml = qhtml.replace('{ANSWERS}', choices);
					}
				break;
				case 'short-answer':
					qhtml = sfReplaceShortAnswer(qhtml);
					qhtml = qhtml.replace('{ANSWERS}', '');
				break;
				case 'ranking-dropdown':
				case 'ranking-dragdrop':
					var nameID = sfGenerateID();
					if(questionsStack[liID].answers.length){

						if(questionsStack[liID].sf_qtype == 'ranking-dropdown'){
							var choices = '<ul class="choices" data-id="' + nameID + '">';
							var select = '<select>';
							for(var n = 0; n < questionsStack[liID].answers.length; n++){
								select += '<option value="' + questionsStack[liID].answers[n].right + '">' + questionsStack[liID].answers[n].right + '</option>';
							}
							select += '</select>';

							for(var n = 0; n < questionsStack[liID].answers.length; n++){
								choices += '<li class="ranking-left">' + questionsStack[liID].answers[n].left + '</li><li class="ranking-right">' + select + '</li><li class="ranking-break">';
							}
							choices += '</ul>';
						}

						if(questionsStack[liID].sf_qtype == 'ranking-dragdrop'){
							var choices = '<ul class="choices" data-id="' + nameID + '">';
							for(var n = 0; n < questionsStack[liID].answers.length; n++){
								choices += '<li class="ranking-left fixed">' + questionsStack[liID].answers[n].left + '</li><li class="ranking-right ui-widget-header dragable">' + questionsStack[liID].answers[n].right + '</li><li class="ranking-break">';
							}
							choices += '</ul>';
						}

						qhtml = qhtml.replace('{ANSWERS}', choices);
					}
				break;
				case 'boilerplate':
					qhtml = qhtml.replace('{ANSWERS}', '');
				break;
				case 'ranking':
				case 'likert-scale':
					var nameID = sfGenerateID();
					

						if(questionsStack[liID].sf_qtype == 'ranking'){
							var choices = '<ul class="choices" data-id="' + nameID + '">';
							var select = '<select>';
							if(questionsStack[liID].answers.ranks.length)
							for(var n = 0; n < questionsStack[liID].answers.ranks.length; n++){
								select += '<option value="' + questionsStack[liID].answers.ranks[n] + '">' + questionsStack[liID].answers.ranks[n] + '</option>';
							}
							select += '</select>';

							if(questionsStack[liID].answers.options.length)
							for(var n = 0; n < questionsStack[liID].answers.options.length; n++){
								choices += '<li class="ranking-left">' + questionsStack[liID].answers.options[n] + '</li><li class="ranking-right">' + select + '</li><li class="ranking-break">';
							}
							choices += '</ul>';
						}

						if(questionsStack[liID].sf_qtype == 'likert-scale'){
							
							var table = '<table class="likert-scale">';
							var thead = '<thead><tr><th></th>';
							
							if(questionsStack[liID].answers.scales.length){
								for(var n = 0; n < questionsStack[liID].answers.scales.length; n++){
									thead += '<th>' + questionsStack[liID].answers.scales[n] + '</th>';
								}
							}
							thead += '</tr></thead>';
							table += thead;

							var tbody = '<tbody>';
							if(questionsStack[liID].answers.options.length){
								
								for(var n = 0; n < questionsStack[liID].answers.options.length; n++){

									var nameID = sfGenerateID();
									tbody += '<tr nameid="' + nameID + '">';
									tbody += '<td>' + questionsStack[liID].answers.options[n] + '</td>';
									if(questionsStack[liID].answers.scales.length){
										for(var m = 0; m < questionsStack[liID].answers.scales.length; m++){
											tbody += '<td><label class="clean-input-wrap"><input type="radio" name="' + nameID + '"><span class="clean-input"></span></label></td>';
										}
									}
									tbody += '</tr>';
								}
							}
							tbody += '</tbody></table>';
							table += tbody;
							choices = table;
						}

						qhtml = qhtml.replace('{ANSWERS}', choices);
					

				break;
				case 'page-break':
					sfAddPage(false, liID);
					$(".placeholder").remove();
					continue;
				break;
			}

			questionsStack[liID].page = currPage;
			$("#survey-questions" + currPage).append(qhtml);
			sfSelectQuestion(liID);
		}

		for(var liID in questionsStack){
			if(questionsStack[liID].hides.length)
			{
				for(var i = 0; i < questionsStack[liID].hides.length; i++){
					var quest_id = questionsStack[liID].hides[i].question;
					var answ = questionsStack[liID].hides[i].answer;

					for(var j in questionsStack){
						if(questionsStack[j].id == quest_id){
							questionsStack[liID].hides[i].question = j;

							switch(questionsStack[j].sf_qtype){
								case 'pick-one':
								case 'pick-many':
									if(questionsStack[j].answers.length)
									for(var k = 0; k < questionsStack[j].answers.length; k++)
									{
										if(questionsStack[j].answers[k].id == answ){
											questionsStack[liID].hides[i].answer = k + 1;
											break;
										}
									}
								break;
								case 'ranking-dragdrop':
								case 'ranking-dropdown':
									if(questionsStack[j].answers.length)
									for(var k = 0; k < questionsStack[j].answers.length; k++)
									{
										if(questionsStack[j].answers[k].leftid == answ){
											questionsStack[liID].hides[i].answer = k + 1;
											break;
										}
									}
								break;
								case 'ranking':
								case 'likert-scale':
									if(questionsStack[j].answers.oid.length)
									for(var k = 0; k < questionsStack[j].answers.oid.length; k++)
									{
										if(questionsStack[j].answers.oid[k] == answ){
											questionsStack[liID].hides[i].answer = k + 1;
											break;
										}
									}
								break;
							}

							break;
						}
					}
				}
			}

			if(questionsStack[liID].rules.length)
			{
				for(var i = 0; i < questionsStack[liID].rules.length; i++){

					var quest_id = questionsStack[liID].rules[i].question;
					var answ = questionsStack[liID].rules[i].answer;
					var opt = questionsStack[liID].rules[i].option;

					switch(questionsStack[liID].sf_qtype){
						case 'pick-one':
						case 'pick-many':
							if(questionsStack[liID].answers.length)
							for(var k = 0; k < questionsStack[liID].answers.length; k++)
							{
								if(questionsStack[liID].answers[k].id == answ){
									questionsStack[liID].rules[i].answer = k + 1;
									break;
								}
							}
						break;
						case 'ranking-dragdrop':
						case 'ranking-dropdown':
							if(questionsStack[liID].answers.length){
								for(var k = 0; k < questionsStack[liID].answers.length; k++)
								{
									if(questionsStack[liID].answers[k].rightid == answ){
										questionsStack[liID].rules[i].answer = k + 1;
										break;
									}
								}

								for(var k = 0; k < questionsStack[liID].answers.length; k++)
								{
									if(questionsStack[liID].answers[k].leftid == opt){
										questionsStack[liID].rules[i].option = k + 1;
										break;
									}
								}
							}
						break;
						case 'ranking':
						case 'likert-scale':
							if(questionsStack[liID].answers.oid.length){
								for(var k = 0; k < questionsStack[liID].answers.oid.length; k++)
								{
									if(questionsStack[liID].answers.oid[k] == opt){
										questionsStack[liID].rules[i].option = k + 1;
										break;
									}
								}

								for(var k = 0; k < questionsStack[liID].answers.oid.length; k++)
								{
									if(questionsStack[liID].sf_qtype == 'ranking'){
										if(questionsStack[liID].answers.rid[k] == answ){
											questionsStack[liID].rules[i].answer = k + 1;
											break;
										}
									} else {
										if(questionsStack[liID].answers.sid[k] == answ){
											questionsStack[liID].rules[i].answer = k + 1;
											break;
										}
									}
								}
							}
						break;
					}

					for(var j in questionsStack){
						if(questionsStack[j].id == quest_id){
							questionsStack[liID].rules[i].question = j;
							break;
						}
					}
				}
			}
		}
	}
	sfSelectPage(1);
}

var sfSelectQuestion = function(liID)
{
	$("#" + liID).bind('click', function(){
		sfClearActives();
		$(this).addClass("active");
		$(this).find("i.remove").css("opacity", "1");
		currQuestion = $(this);
		sfGetOptions(liID);
		return true;
	});	
}

var sfAddPage = function(newPageBreak, liID)
{
	if(newPageBreak){
		var liID = sfGenerateID();
		questionsStack[liID] = {};
		questionsStack[liID].exists = 1;
		questionsStack[liID].published = 1;
		questionsStack[liID].sf_qtitle = "Page Break";
		questionsStack[liID].sf_qtype = 'page-break';
		questionsStack[liID].is_final_question = 0;
		questionsStack[liID].sf_compulsory = 0;
		questionsStack[liID].sf_default_hided = 0;
		questionsStack[liID].questOrdering = questOrdering;

		questOrdering++;
	}

	var prevPage = lastPage;
	lastPage = lastPage + 1;
	
	var titleHtml = $("#page" + prevPage + " div.title").html();
	$("#page" + currPage).hide();

	var pageHtml = '<div class="pages" id="page' + lastPage + '" questid="' + liID + '"><div class="title">' + titleHtml + '</div><ol id="survey-questions' + lastPage + '" class="page active"><li class="placeholder" style="display: list-item;">' + COM_SF_YOU_HAVANT_ADDED_WITH_SLASH + '</li></ol></div>';
	$(pageHtml).insertAfter(".viewport #page" + prevPage);
	$("#page" + lastPage).show();

	$("#tab" + currPage).removeClass("active");
	var tabHtml = '<a style="" name="0" class="button button-tab-bottom page-button active" href="#" id="tab' + lastPage + '" onclick="javascript:sfSelectPage(' + lastPage + ');">' + lastPage + '</a>';
	$(".viewport .tabs").append(tabHtml);

	$( "#survey-questions" + lastPage ).droppable({
		drop: function( event, ui ) {
			qtype = $(ui.helper).attr("field-type");
			sfAddQuestion(qtype);
		}
	}).sortable({
		axis: "y",
		placeholder: "ui-state-highlight",
		cursor: "move",
		stop: function( event, ui )
		{
			sf_SortQuestions( event, ui );
		}
	});

	currQuestion = null;
	$("ol.page li.field").removeClass("active");
	$("ol.page li.field i.remove").css("opacity", "0.2");
	currPage = lastPage;
	sfRemoveChoicesPanel();
	sfDisableOptions();
	sf_SortQuestions();
	return true;
}

var sfSelectPage = function(selectPage)
{
	$("#page" + currPage).hide();
	$("#page" + selectPage).show();

	$(".viewport .tabs #tab" + currPage).removeClass("active");
	$(".viewport .tabs #tab" + selectPage).addClass("active");

	$( "#survey-questions" + selectPage ).droppable({
		drop: function( event, ui ) {
			qtype = $(ui.helper).attr("field-type");
			sfAddQuestion(qtype);
		}
	}).sortable({
		axis: "y",
		placeholder: "ui-state-highlight",
		cursor: "move",
		stop: function( event, ui )
		{
			sf_SortQuestions( event, ui );
		}
	});

	currPage = selectPage;
	currQuestion = null;
	$("ol.page li.field").removeClass("active");
	$("ol.page li.field i.remove").css("opacity", "0.2");
	sfRemoveChoicesPanel();
	sfDisableOptions();
	return true;
}

var sfDeletePage = function()
{
	if(lastPage > 1)
	$( "#dialog-pageremove-confirm" ).dialog({
		autoOpen: true,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			Yes: function() {
				sfRemovePage();
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});
}

var sfRemovePage = function()
{
	var deleteQuestions = [];
	
	for(var question in questionsStack){
		if(questionsStack[question].page == currPage){
			deleteQuestions.push(question);
		}
	}
		
	if(deleteQuestions.length)
		for(var i =0; i < deleteQuestions.length; i++){
			delete(questionsStack[deleteQuestions[i]]);
		}

	var questid = $("#page" + currPage).attr("questid");
	delete(questionsStack[questid]);

	$("#page" + currPage).remove();
	$(".viewport .tabs #tab" + currPage).remove();

	var passOnPage = sfReorderPages();

	$("#page" + passOnPage).show();
	$(".viewport .tabs #tab" + passOnPage).addClass("active");

	currPage = passOnPage;
	currQuestion = null;
	$("ol.page li.field").removeClass("active");
	$("ol.page li.field i.remove").css("opacity", "0.2");
	sfRemoveChoicesPanel();
	sfDisableOptions();
	return true;
}

var sfReorderPages = function()
{
	var passOnPage = currPage - 1;
	if(!passOnPage){
		passOnPage = currPage;
	}

	for(var n = currPage + 1; n <= lastPage; n++){
		for(question in questionsStack){
			if(questionsStack[question].page == n){
				questionsStack[question].page = n - 1;
			}
		}
	}

	for(var i = currPage + 1; i <= lastPage; i++){
		if($("#page" + i)){
			var newi = i - 1;
			$("#page" + i).attr("id", "page" + newi);
			$("#survey-questions" + i).attr("id", "survey-questions" + newi);
			$(".viewport .tabs #tab" + i).text(newi);
			$(".viewport .tabs #tab" + i).attr("onclick", "javascript:sfSelectPage(" + newi + ");");
			$(".viewport .tabs #tab" + i).attr("id", "tab" + newi);
		}
	}

	lastPage = lastPage - 1;
	return passOnPage;
}

var sfOrderingPages = function(event, ui)
{	
	var dragPage = parseInt($(ui.item).text());
	var prevPage = parseInt($(ui.item).prev().text());
	var nextPage = parseInt($(ui.item).next().text());

	var tabs = $(".viewport .tabs a");
	
	if(prevPage || nextPage){
		if(prevPage){
			$("#page" + dragPage).insertAfter("#page" + prevPage);
		} else if(nextPage){
			$("#page" + dragPage).insertBefore("#page" + nextPage);
		}
	}
	
	var pages = $(".viewport .pages");
	var n = 1;
	$(pages).each(function(i){
		
		var currentLi = $(pages[i]).find(".page li").not(".placeholder");
		if(currentLi.length)
		$(currentLi).each(function(j){
			var currentLiID = $(currentLi[j]).attr("id");
			if(questionsStack[currentLiID])
				questionsStack[currentLiID].page = n;
		});

		n++;
	});

	var n = 1;
	$(pages).each(function(i){
		$(pages[i]).attr("id", "page" + n);
		$(pages[i]).find(".page").attr("id", "survey-questions" + n);
		$(tabs[i]).text(n);
		$(tabs[i]).attr("onclick", "javascript:sfSelectPage(" + n + ");");
		$(tabs[i]).attr("id", "tab" + n);
		n++;
	});
	
	currPage = parseInt($(".viewport .tabs a.active").text());
	return true;
}

var sfOpenCKEEditor = function(editor_button, type){

	var html = $(editor_button).parent().prev().val();
	CKEDITOR.instances.CKeditor.destroy();
	
	$("#CKeditor").val(html);
	CKEDITOR.replace('CKeditor');	    	

	$( "#dialog-editor" ).dialog({
		autoOpen: true,
		width: 700,
		modal: true,
		buttons: {
			Ok: function() {
				var editor_data = CKEDITOR.instances.CKeditor.getData();
				$(editor_button).parent().prev().val(editor_data);
				$("#CKeditor").val(html);

				var liID = $(currQuestion).attr('id');

				switch(type){
					case 'questionTitle':
						questionsStack[liID].sf_qtitle = editor_data;

						if(questionsStack[liID].sf_qtype == 'short-answer') editor_data = sfReplaceShortAnswer(editor_data);
						$(currQuestion).find("h3.title").html(editor_data);

					break;
					case 'questionDescr':
						$(currQuestion).find("div.description").html(editor_data);
						questionsStack[liID].sf_qdescription = editor_data;
					break;
					case 'surveyDescr':
						$(".viewport div.title p.description").html(editor_data);
					break;
				}

				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	return true;
}

var sfClone = function(obj){

	var newObj = {}, i;
	
	for(i in obj){
		if(obj[i] instanceof Array){

			var newArray = [];
			if(obj[i].length)
			for(var n = 0; n < obj[i].length; n++){
				newArray[n] = {};
				for(var j in obj[i][n])
					newArray[n][j] = obj[i][n][j];
			}

			newObj[i] = newArray;

		} else {
			newObj[i] = obj[i];
		}
	}
	
	return newObj;
}

var sfMoveQuestionToAction = function(toPage)
{
	$("#page" + toPage + " .placeholder").remove();
	var liID = $(currQuestion).attr("id");
	questionsStack[liID].page = toPage;

	$("#page" + toPage + " #survey-questions" + toPage).append(currQuestion);
	$("#page" + toPage).css("display", "block");
	$("#page" + currPage).css("display", "none");

	$(".viewport .tabs a").removeClass("active");
	$(".viewport .tabs #tab" + toPage).addClass("active");

	currPage = toPage;
	sf_SortQuestions();
	return true;
}

var sfMoveQuestionTo = function()
{
	$("#sf_move_to").html("");
	$("#sf_move_to").html("<option value=''>"+COM_SURVEYFORCE_SELECT_PAGE+"</option>");

	for(var n = 1; n <= lastPage; n++){
		if (n == currPage) continue;
		var option = "<option value='" + n + "'>" + n + "</oprion>";
		$("#sf_move_to").append(option);
	}

	$( "#dialog-move-to" ).dialog({
		autoOpen: true,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			Move: function() {
				var toPage = $("#sf_move_to").val();
				if(toPage == ""){
					alert(COM_SURVEYFORCE_SELECT_PAGE);
					return true;
				}
				sfMoveQuestionToAction(toPage);
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	return true;
}

var sfDublicateQuestion = function()
{
	var oldLiID = $(currQuestion).attr("id");
	var newLiID = sfGenerateID();
	var liclone = $(currQuestion).clone();
	$(liclone).attr("id", newLiID);
	$("#survey-questions" + currPage).append(liclone);

	questionsStack[newLiID] = sfClone(questionsStack[oldLiID]);

	sfClearActives();
	$("#" + newLiID).addClass("active");
	$("#" + newLiID + " i.remove").css("opacity", "1");
	$("#survey-questions" + currPage).selectable();

	$("#" + newLiID).click(function(){
		sfClearActives();
		$(this).addClass("active");
		$(this).find("i.remove").css("opacity", "1");
		currQuestion = $(this);
		sfGetOptions(newLiID);
		return true;
	});

	if($("#" + newLiID).attr("name") == 'pick-one')
	{
		var newNameID = sfGenerateID();
		$("#" + newLiID).find(".choices").attr("data-id", newNameID);
		$("#" + newLiID).find(".choices li.choice input").attr("name", newNameID);
	}

	currQuestion = liclone;
	sf_SortQuestions();
	return true;
}

var sfGoToAddQuestion = function()
{
	$(".tab-pane").removeClass("active");
	$(".nav-tabs li").removeClass("active");

	$("#questionsButton").addClass("active");
	$("#questions").addClass("active");
	return true;
}

var sfChangeQuestionTitle = function()
{
	var value = $("textarea[name='sf_qtitle']").val();
	var liID = $(currQuestion).attr("id");
	questionsStack[liID]['sf_qtitle'] = value;
	if(questionsStack[liID].sf_qtype == 'short-answer') value = sfReplaceShortAnswer(value);
	$("li#" + liID).find("h3.title").html(value);
	
	refreshQuestionsList();
	changeQuestionInHides();

	return true;
}

var sfChangeQuestionDescription = function()
{
	var value = $("textarea[name='sf_qdescription']").val();
	var liID = $(currQuestion).attr("id");

	$("li#" + liID).find("div.description").html(value);
	questionsStack[liID]['sf_qdescription'] = value;
	return true;
}

var sfSelectCheckbox = function(checkbox)
{
	var prop = $(checkbox).attr("name");
	var liID = $(currQuestion).attr("id");
	if($(checkbox).prop("checked")){
		var value = 1;
	} else if(!$(checkbox).prop("checked")) {
		var value = 0;
	}

	questionsStack[liID][prop] = value;
	return true;
}

var sfRemoveImage = function()
{
	$("#image_file").val("");
	$("#sf_image").val("");
	$("#bkg_thumb span").remove();
	$(".pages").css("background", "white");
	return true;
}

var sfSelectFile = function(object)
{
	$(object).next().click();
	file = object;
	fileInterval = setInterval("sfCheckSelectFile()", 300);
	
	return true;
}

var sfCheckSelectFile = function()
{
	if($(file).next().val() != ''){
		var filename = $(file).next().val();
		clearInterval(fileInterval);
		$(file).prev().val(filename);

		if (window.File && window.FileReader && window.FileList && window.Blob) {
  			
			document.getElementById('image_file').addEventListener('change', handleFileSelect, false);

		} else {
  			alert(COM_SURVEYFORCE_FILE_API_ARE_NOT_SUPPORTED);
		}

		return true;
	}

	return true;
}

var handleFileSelect = function(evt) {

    var files = evt.target.files; // FileList object

    // Loop through the FileList and render image files as thumbnails.
    for (var i = 0, f; f = files[i]; i++) {

      // Only process image files.
      if (!f.type.match('image.*')) {
        continue;
      }

      var reader = new FileReader();

      // Closure to capture the file information.
      reader.onload = (function(theFile) {
        return function(e) {
          $("#bkg_thumb span").remove();
          // Render thumbnail.
          var span = document.createElement('span');
          span.innerHTML = ['<img class="thumb" src="', e.target.result,
                            '" title="', escape(theFile.name), '"/>'].join('');
          $("#bkg_thumb").append(span);
          $(".pages").css("background", "url(" + e.target.result + ")");
        };
      })(f);

      // Read in the image file as a data URL.
      reader.readAsDataURL(f);
    }
}

var sfOpenEditButton = function(object)
{
	$(object).next().slideDown();
	return true;
}

var sfCloseEditButton = function(object)
{
	$(object).next().slideUp();
	return true;
}

var sfChangeSurveyName = function()
{
	var sf_name = $("#sf_name").val();
	$("h2.title").text(sf_name);
	return true;
}

var sfChangeSurveyDescr = function()
{
	var sf_descr = $("#sf_descr").val();
	$("p.description").html(sf_descr);
	return true;
}

var sfDeleteQuestion = function(remove_button)
{
	if(remove_button){
		var li = $(remove_button).parent();
	} else {
		var li = currQuestion;
	}
	var liID = $(li).attr("id");

	$(li).fadeOut(400, function(){
		$(li).remove();
		$("#fieldProperties").css("display", "none");
		$("#Settings").css("display", "none");

		$("#fieldPropertiesDisable").css("display", "block");
		$("#SettingsDisable").css("display", "block");

		$("#panelActions").css("display", "none");
		sfRemoveChoicesPanel();
	});
	delete(questionsStack[liID]);
	
	currQuestion = null;
	
	refreshQuestionsList();
	refreshAnswersList();
	sf_SortQuestions();

	return true;
}

var sfRemoveQuestion = function(remove_button){

	$( "#dialog-confirm" ).dialog({
		autoOpen: true,
		height: 300,
		width: 350,
		modal: true,
		buttons: {
			Yes: function() {
				sfDeleteQuestion(remove_button);
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	return true;	    	
}

var sfRefreshBulkList = function(select)
{
	var textareaString = "";
	var currBulk = $(select).val();
	if(BULKS.length)
		for(var n = 0; n < BULKS.length; n++)
		{
			bulkObject = BULKS[n];
			if(currBulk == bulkObject.name){

				var values = bulkObject.values;
				for(var i=0; i < values.length; i++){
					var value = bulkObject.values[i];
					textareaString += bulkObject.values[i].en + "\n";
				}

				$("#bulk_list").val(textareaString);
				break;
			}
		}

	return true;
}

var sfAddBulkList = function(currBulk, type)
{
	var liID = $(currQuestion).attr("id");

	questionsStack[liID].answers[0]['other_option'] = 0;
	questionsStack[liID].answers[0]['other_option_text'] = '';
	delete(questionsStack[liID].answers);

	questionsStack[liID].answers = [];

	var questStyle = questionsStack[liID].choiceStyle;

	if(!questStyle){
		$("#" + liID + " .choices li").remove();
	} else {
		$("#" + liID + " .choices option").remove();
	}
	$("#choices-list li").remove();

	if(BULKS.length)
		for(var n = 0; n < BULKS.length; n++)
		{
			bulkObject = BULKS[n];
			if(currBulk == bulkObject.name){
				var values = bulkObject.values;
				for(var i=0; i < values.length; i++){
					sfAddChoice(bulkObject.values[i].en, true, type);
				}

				break;
			}
			
		}

	$("#other_option_cb").prop("checked", false);
	$("#other_option").val('');

	return true;
}

var sfDialogBulkList = function(type)
{
	$( "#dialog-bulk" ).dialog({
		autoOpen: true,
		height: 400,
		width: 550,
		modal: true,
		buttons: {
			Add: function() {
				var currBulk = $("#bulk-selector").val();
				sfAddBulkList(currBulk, type);
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});

	return true;
}

var sfAddQuestion = function(qtype)
{
	var liID = sfGenerateID();

	switch(qtype){
		case 'section-separator':
			var qhtml = '<li class="field" name="section-separator" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Section Heading</h3><div class="description"></div></li>';
		break;
		case 'pick-one':
			var nameID = sfGenerateID();
			var qhtml = '<li class="field" name="pick-one" style="" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + nameID + '"><li class="choice"><label><label class="clean-input-wrap"><input type="radio" name="' + nameID + '"><span class="clean-input"></span></label> <span class="choice-value">Choice 1</span></label></li><li class="choice"><label><label class="clean-input-wrap"><input type="radio" name="' + nameID + '"><span class="clean-input"></span></label> <span class="choice-value">Choice 2</span></label></li></ul></li>';
		break;
		case 'pick-many':
			var nameID = sfGenerateID();
			var qhtml = '<li class="field" name="pick-many" style="" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + nameID + '"><li class="choice"><label><label class="clean-input-wrap"><input type="checkbox" name="' + nameID + '"><span class="clean-input"></span></label> <span class="choice-value">Choice 1</span></label></li><li class="choice"><label><label class="clean-input-wrap"><input type="checkbox" name="' + nameID + '"><span class="clean-input"></span></label><span class="choice-value">Choice 2</span></label></li></ul></li>';
		break;
		case 'ranking-dropdown':
			var nameID = sfGenerateID();
			var qhtml = '<li class="field" name="ranking-dropdown" style="" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + nameID + '"><li class="ranking-left">Option 1</li><li class="ranking-right"><select><option value="Rank 1">Rank 1</option><option value="Rank 2">Rank 2</option></select></li><li class="ranking-break"></li><li class="ranking-left">Option 2</li><li class="ranking-right"><select><option value="Rank 1">Rank 1</option><option value="Rank 2">Rank 2</option></select></li><li class="ranking-break"></li></ul></li>';
		break;
		case 'short-answer':
			var qhtml = '<li class="field" name="short-answer" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Every {x} in question text will be replaced by input box. If the number of {x} is more than zero no large text area will be displayed. To place text area with input box in question text use {y} tag.</h3><div class="description"></div></li>';
			qhtml = sfReplaceShortAnswer(qhtml);
		break;
		case 'ranking-dragdrop':
			var nameID = sfGenerateID();
			var qhtml = '<li class="field" name="ranking-dragdrop" style="" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + nameID + '"><li class="ranking-left fixed">Option 1</li><li class="ranking-right ui-widget-header dragable">Rank 1</li><li class="ranking-break"></li><li class="ranking-left fixed">Option 2</li><li class="ranking-right ui-widget-header dragable">Rank 2</li><li class="ranking-break"></li></ul></li>';
		break;
		case 'boilerplate':
			var qhtml = '<li class="field" name="section-separator" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Boilerplate</h3><div class="description"></div></li>';
		break;
		case 'page-break':
			sfAddPage(true);
			return true;
		break;
		case 'ranking':
			var nameID = sfGenerateID();
			var qhtml = '<li class="field" name="ranking" style="" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><ul class="choices" data-id="' + nameID + '"><li class="ranking-left">Option 1</li><li class="ranking-right"><select><option value="1">1</option><option value="2">2</option></select></li><li class="ranking-break"></li><li class="ranking-left">Option 2</li><li class="ranking-right"><select><option value="1">1</option><option value="2">2</option></select></li><li class="ranking-break"></li></ul></li>';
		break;
		case 'likert-scale':
			var nameID = sfGenerateID();
			var nameID2 = sfGenerateID();
			var qhtml = '<li class="field" name="likert-scale" style="" id="' + liID + '"><i class="fa fa-times remove" onclick="javascript:sfRemoveQuestion(this);"></i><span class="status"></span><span class="name" title="Identifier"></span><span class="nclass" title="Extra Classes"></span><h3 class="title">Question Text</h3><div class="description"></div><table class="likert-scale"><thead><tr><th></th><th>Scale 1</th><th>Scale 2</th><th>Scale 3</th><th>Scale 4</th></tr></thead><tbody><tr nameid="' + nameID + '"><td>Option 1</td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID + '"><span class="clean-input"></span></label></td></tr><tr nameid="' + nameID2 + '"><td>Option 2</td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID2 + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID2 + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID2 + '"><span class="clean-input"></span></label></td><td><label class="clean-input-wrap"><input type="radio" name="' + nameID2 + '"><span class="clean-input"></span></label></td></tr></tbody></table></li>';
		break;
	}

	if(qtype) sfInsertQuestion(qhtml, liID);
	return true;
}

var sfReplaceShortAnswer = function(html)
{
	var html = html.replace(/{x}/g, '<input type="text" class="text" style="width: 200px;" value="">');
	var html = html.replace(/{y}/g, '<textarea class="text" style="width: 400px;height:200px;"></textarea>');

	return html;
}

var sfEnableOptions = function()
{
	if(currQuestion){
		$("#fieldPropertiesDisable").hide();
		$("#fieldProperties").show();
		$("#SettingsDisable").hide();
		$("#Settings").show();

		$("#panelActions").css("display", "block");
	} else {
		sfDisableOptions();
	}
}

var sfDisableOptions = function()
{
	$("#fieldPropertiesDisable").show();
	$("#fieldProperties").hide();
	$("#SettingsDisable").show();
	$("#Settings").hide();

	$("#panelActions").css("display", "none");
}

var sfSetSelected = function(name, value)
{
	var isSelect = false;
	var select= $("select[name='" + name + "']").find("option");
	for(var n=0; n < select.length; n++){
		var option = select[n];
		if($(option).val() == parseInt(value)){
			$(option).attr("selected", "selected");
			$(option).prop("selected", true);
		} else {
			$(option).removeAttr("selected");
			$(option).prop("selected", false);
		}
	}

	return true;
}

var sfSetCheckbox = function(name, value)
{
	if(value){
		$("input[name='" + name + "']").attr("checked", "checked");
		$("input[name='" + name + "']").prop("checked", true);
	} else if(!value) {
		$("input[name='" + name + "']").attr("checked", "");
		$("input[name='" + name + "']").prop("checked", false);
	}

	return true;
}

var sfSetIscale = function (value) {
	var liID = $(currQuestion).attr("id");
	questionsStack[liID].sf_iscale = value;
}

var sfSetOptionsFields = function(liID)
{
	var sf_qtype = (questionsStack[liID].sf_qtype) ? questionsStack[liID].sf_qtype : "section-separator";
	var sf_qtitle = (questionsStack[liID].sf_qtitle) ? questionsStack[liID].sf_qtitle : "Enter Question Text";
	var sf_qdescription = (questionsStack[liID].sf_qdescription) ? questionsStack[liID].sf_qdescription : "";
	var sf_iscale = (questionsStack[liID].sf_iscale) ? questionsStack[liID].sf_iscale : "";
	var published = (typeof(questionsStack[liID].published) != 'undefined') ? questionsStack[liID].published : 1;
	var sf_compulsory = (typeof(questionsStack[liID].sf_compulsory) != 'undefined') ? questionsStack[liID].sf_compulsory : 0;
	var sf_default_hided = (typeof(questionsStack[liID].sf_default_hided) != 'undefined') ? questionsStack[liID].sf_default_hided : 0;
	var is_final_question = (typeof(questionsStack[liID].is_final_question) != 'undefined') ? questionsStack[liID].is_final_question : 0;

	sfSetSelected('sf_qtype', sf_qtype);
	$("textarea[name='sf_qtitle']").val(sf_qtitle);
	$("textarea[name='sf_qdescription']").val(sf_qdescription);
	sfSetSelected('sf_iscale', sf_iscale);

	sfSetCheckbox('published', published);
	sfSetCheckbox('sf_compulsory', sf_compulsory);
	sfSetCheckbox('sf_default_hided', sf_default_hided);
	sfSetCheckbox('is_final_question', is_final_question);
}

var sfChangeRightOption = function(object)
{
	var liID = $(currQuestion).attr("id");
	var lirank = $(object).parent().parent();
	var index = $(lirank).index();

	var rightText = $(object).val();
	questionsStack[liID].answers[index].right = rightText;

	var selects = $(currQuestion).find(".choices .ranking-right select");
	if(selects.length){
		$(selects).each(function(i){
			var option = $(selects[i]).find("option").get(index);

			$(option).val(rightText);
			$(option).text(rightText);
		});
	} else {
		var rightRank = $(currQuestion).find(".choices li.ranking-right");
		rightRank = $(rightRank).get(index);
		$(rightRank).text(rightText);
	}

	refreshAnswersList();

	return true;
}

var sfChangeLeftOption = function(object)
{
	var liID = $(currQuestion).attr("id");
	var lirank = $(object).parent().parent();
	var index = $(lirank).index();

	var leftText = $(object).val();
	questionsStack[liID].answers[index].left = leftText;

	var changeElems = $(currQuestion).find(".choices .ranking-left");
	$(changeElems[index]).text(leftText);

	refreshAnswersList();

	return true;
}

var sfChangeChoice = function(object)
{
	var liID = $(currQuestion).attr("id");
	var lichoice = $(object).parent().parent();
	var index = $(lichoice).index();

	var liText = $(object).val();

	questionsStack[liID].answers[index].title = liText;
	var questStyle = questionsStack[liID].choiceStyle;

	if(!questStyle){
		var changeElem = $(currQuestion).find(".choices li").get(index);
		$(changeElem).find(".choice-value").text(liText);
	} else {
		var changeElem = $(currQuestion).find(".choices option").get(index);
		$(changeElem).text(liText);
	}

	refreshAnswersList();
	changeAnswersInHides(index);

	return true;
}

var sfRemoveScale = function(object)
{
	var liID = $(currQuestion).attr("id");
	var liscale = $(object).parent().parent();
	var index = $(liscale).index();

	questionsStack[liID].answers.scales.splice(index - 1, 1);
	questionsStack[liID].answers.sid.splice(index - 1, 1);

	$(object).parent().parent().fadeOut(300, function(){
		$(this).remove();
		var th = $(currQuestion).find(".likert-scale th").get(index);
		$(th).remove();

		var trs = $(currQuestion).find(".likert-scale tr");
		$(trs).each(function(n){
			var td = $(trs[n]).find("td").get(index);
			$(td).remove();
		});
	});

	refreshAnswersList();
	removeAnswersInHides(index - 1);

	return true;
}

var sfRemoveRank = function(object)
{
	var liID = $(currQuestion).attr("id");
	var lirank = $(object).parent().parent();
	var index = $(lirank).index();

	questionsStack[liID].answers.splice(index, 1);

	$(object).parent().parent().fadeOut(300, function(){
		$(this).remove();

		var removeElems = $(currQuestion).find(".choices li.ranking-left");
		var nextRemoveElem = $(removeElems[index]).next();

		$(removeElems[index]).remove();
		$(nextRemoveElem).remove();

		var select = $(currQuestion).find(".choices li.ranking-right select");
		$(select).each(function(i){
			var option = $(select[i]).find("option").get(index);
			$(option).remove();
		});
	});

	refreshAnswersList();
	removeAnswersInHides(index);

	return true;
}

//Remove rank in ranking questions
var sfRemoveRank2 = function(object)
{
	var liID = $(currQuestion).attr("id");
	var lirank = $(object).parent().parent();
	var index = $(lirank).index();
	index--;

	questionsStack[liID].answers.ranks.splice(index, 1);
	questionsStack[liID].answers.rid.splice(index, 1);

	$(object).parent().parent().fadeOut(300, function(){
		$(this).remove();

		var select = $(currQuestion).find(".choices li.ranking-right select");
		$(select).each(function(i){
			var option = $(select[i]).find("option").get(index);
			$(option).remove();
		});
	});

	refreshAnswersList();
	removeAnswersInHides(index);

	return true;
}

var sfRemoveOption = function(object, qtype)
{
	var liID = $(currQuestion).attr("id");
	var li = $(object).parent().parent();
	var index = $(li).index();
	index--;

	questionsStack[liID].answers.options.splice(index, 1);
	questionsStack[liID].answers.oid.splice(index, 1);

	$(object).parent().parent().fadeOut(300, function(){
		$(this).remove();

		if(qtype == 'ranking'){

			var left = $(currQuestion).find(".choices .ranking-left").get(index);
			var right = $(currQuestion).find(".choices .ranking-right").get(index);
			var brk = $(currQuestion).find(".choices .ranking-break").get(index);

			$(left).remove();
			$(right).remove();
			$(brk).remove();

		} else if(qtype == 'likert-scale'){

			var tr = $(currQuestion).find(".likert-scale tr").get(index + 1);
			$(tr).remove();

		}

	});

	refreshAnswersList();

	return true;
}

var sfRemoveChoice = function(object)
{
	var liID = $(currQuestion).attr("id");
	var lichoice = $(object).parent().parent();
	var index = $(lichoice).index();

	questionsStack[liID].answers.splice(index, 1);
	var questStyle = questionsStack[liID].choiceStyle;

	$(object).parent().parent().fadeOut(300, function(){
		$(this).remove();

		if(!questStyle){
			var removeElem = $(currQuestion).find(".choices li").get(index);
		} else {
			var removeElem = $(currQuestion).find(".choices option").get(index);
		}

		$(removeElem).remove();
	});

	refreshAnswersList();
	removeAnswersInHides(index);

	return true;
}

var sfGetChoicesPanel = function()
{
	var phtml = '<div class="panel-group" id="panelChoices"><div class="panel panel-default"><div class="panel-heading" data-toggle="collapse" data-parent="#panelChoices" href="#collapseChoices"><h4>Choices<i class="fa fa-sort-desc" style="float:right;"></i></h4></div><div style="height: auto;" class="panel-collapse collapse in" id="collapseChoices"><div class="panel-body">{PANEL_BODY}</div></div></div></div>';

	return phtml;
}

var sfAddQuestionOption = function(phtml)
{

	var startIndex;
	
	if(phtml != ''){
		$(phtml).insertAfter("#panelProperties");
		$("#choices-list").sortable({
			axis: 'y',
			placeholder: "ui-state-highlight",
			cursor: "move",
			start: function( event, ui ) {
				startIndex = sfStartIndex(event, ui);
			},
			stop: function( event, ui )
			{
				sfChangeChoiceOrdering( event, ui, startIndex );
			}
		});

		$("#ranking-list").sortable({
			axis: 'y',
			placeholder: "ui-state-highlight",
			cursor: "move",
			start: function( event, ui ) {
				startIndex = sfStartIndex(event, ui);
			},
			stop: function( event, ui )
			{
				sfChangeRankOrdering( event, ui, startIndex );
			}
		});

		$("#panelChoices").find(".option a").unbind("click");
		$("#panelChoices").find(".option a").click(function(){
			sfRemoveChoice(this);
		});

		$("#panelChoices").find(".option input").unbind("keyup");
		$("#panelChoices").find(".option input").keyup(function(){
			sfChangeChoice(this);
		});

		$("#panelChoices").find(".rank-left input.ranking-text").unbind("keyup");
		$("#panelChoices").find(".rank-left input.ranking-text").keyup(function(){
			sfChangeLeftOption(this);
		});

		$("#panelChoices").find(".rank-right input.ranking-text").unbind("keyup");
		$("#panelChoices").find(".rank-right input.ranking-text").keyup(function(){
			sfChangeRightOption(this);
		});

		$("#panelChoices").find(".rank-right a").unbind("click");
		$("#panelChoices").find(".rank-right a").click(function(){
			sfRemoveRank(this);
		});

		bindRankEvents();
		bindLikertEvents();

		if ($.fn.selectpicker) {
	   		$(".selectpicker").selectpicker()
		}

	}

	return true;
}

var bindLikertEvents = function()
{
	$("#panelChoices").find("#scale-list .scale .scale-text").unbind("keyup");
	$("#panelChoices").find("#scale-list .scale .scale-text").keyup(function(){
		sfChangeScale(this);
	});

	$("#panelChoices").find("#option-list-likert .options .option-text").unbind("keyup");
	$("#panelChoices").find("#option-list-likert .options .option-text").keyup(function(){
		sfChangeOption(this, 'likert-scale');
	});

	$("#panelChoices").find("#scale-list .scale a.scale-remove").unbind("click");
	$("#panelChoices").find("#scale-list .scale a.scale-remove").click(function(){
		sfRemoveScale(this);
	});

	$("#panelChoices").find("#option-list-likert .options a.option-remove").unbind("click");
	$("#panelChoices").find("#option-list-likert .options a.option-remove").click(function(){
		sfRemoveOption(this, 'likert-scale');
	});

	$("#scale-list").sortable({
		axis: 'y',
		placeholder: "ui-state-highlight",
		cursor: "move",
		start: function( event, ui ) {
			startIndex = sfStartIndex(event, ui);
		},
		stop: function( event, ui )
		{
			sfChangeScaleOrdering( event, ui, startIndex );
		}
	});

	$("#option-list-likert").sortable({
		axis: 'y',
		placeholder: "ui-state-highlight",
		cursor: "move",
		start: function( event, ui ) {
			startIndex = sfStartIndex(event, ui);
		},
		stop: function( event, ui )
		{
			sfChangeOptionOrdering( event, ui, startIndex, 'likert-scale' );
		}
	});
}

var bindRankEvents = function()
{
	$("#panelChoices").find("#rank-list .rank .rank-text").unbind("keyup");
	$("#panelChoices").find("#rank-list .rank .rank-text").keyup(function(){
		sfChangeRank(this);
	});

	$("#panelChoices").find("#option-list-ranking .options .option-text").unbind("keyup");
	$("#panelChoices").find("#option-list-ranking .options .option-text").keyup(function(){
		sfChangeOption(this, 'ranking');
	});

	$("#panelChoices").find("#rank-list .rank a.rank-remove").unbind('click');
	$("#panelChoices").find("#rank-list .rank a.rank-remove").click(function(e){
		sfRemoveRank2(this);
	});

	$("#panelChoices").find("#option-list-ranking .options a.option-remove").unbind("click");
	$("#panelChoices").find("#option-list-ranking .options a.option-remove").click(function(){
		sfRemoveOption(this, 'ranking');
	});

	$("#rank-list").sortable({
		axis: 'y',
		placeholder: "ui-state-highlight",
		cursor: "move",
		start: function( event, ui ) {
			startIndex = sfStartIndex(event, ui);
		},
		stop: function( event, ui )
		{
			sfChangeRankOrdering2( event, ui, startIndex );
		}
	});

	$("#option-list-ranking").sortable({
		axis: 'y',
		placeholder: "ui-state-highlight",
		cursor: "move",
		start: function( event, ui ) {
			startIndex = sfStartIndex(event, ui);
		},
		stop: function( event, ui )
		{
			sfChangeOptionOrdering( event, ui, startIndex, 'ranking' );
		}
	});

}

var sfAddRank = function(qtype)
{
	var liID = $(currQuestion).attr("id");
	
	if(qtype == 'ranking'){
		questionsStack[liID].answers.ranks.push("New rank");
		questionsStack[liID].answers.rid.push("");
		var rhtml = '<li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" value=" New rank" class="rank-text"><a href="#" class="remove rank-remove"><i class="fa fa-times"></i></a></div></li>';
		$("#rank-list").append(rhtml);

		var select = $(currQuestion).find(".choices .ranking-right select");
		$(select).each(function(i){
			var option = '<option value="New rank">New rank</option>';
			$(select[i]).append(option);
		});

		bindRankEvents(qtype);

	} else if(qtype == 'likert-scale'){
		questionsStack[liID].answers.scales.push("New scale");
		questionsStack[liID].answers.sid.push("");
		var rhtml = '<li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" value="New scale" class="scale-text"><a href="#" class="remove scale-remove"><i class="fa fa-times"></i></a></div></li>';
		$("#scale-list").append(rhtml);

		var trh = $(currQuestion).find(".likert-scale tr:first");
		if(trh){
			$(trh).append("<th>New scale</th>");
		} else {
			$(".likert-scale").append("<thead><tr><th></th><th>New scale</th></tr></thead><tbody></tbody>")
		}

		var tr = $(currQuestion).find(".likert-scale tr").not("tr:first");
		if(tr.length){
			$(tr).each(function(n){
				var nameid = $(tr[n]).attr('nameid');
				var newTd = '<td class="ui-selectee"><label class="clean-input-wrap ui-selectee"><input type="radio" name="' + nameid + '" class="ui-selectee"><span class="clean-input ui-selectee"></span></label></td>';
				$(tr[n]).append(newTd);
			});
		}

		bindLikertEvents(qtype);
	}

	refreshAnswersList();

	return true;
}

var sfAddOption = function(qtype)
{
	var liID = $(currQuestion).attr("id");
	questionsStack[liID].answers.options.push("New option");
	questionsStack[liID].answers.oid.push("");
	var rhtml = '<li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" value="New option" class="option-text"><a href="#" class="remove option-remove"><i class="fa fa-times"></i></a></div></li>';
	
	if(qtype == 'ranking'){

		$("#option-list-ranking").append(rhtml);

		var ranks = questionsStack[liID].answers.ranks;
		if(ranks.length){
			var select = '<select class="ui-selectee">';
			for(var n=0; n < ranks.length; n++){
				select += '<option value="' + ranks[n] + '">' + ranks[n] + '</option>';
			}
			select += '</select>';
		}

		var ohtml = '<li class="ranking-left ui-selectee">New option</li><li class="ranking-right ui-selectee">' + select + '</li><li class="ranking-break ui-selectee"></li>';
		$(currQuestion).find(".choices").append(ohtml);
	} else {

		$("#option-list-likert").append(rhtml);
		var tbody = $(currQuestion).find(".likert-scale tbody");

		var nameid = sfGenerateID();
		var tr = '<tr nameid="' + nameid + '"><td>New option</td>';
		var th = $(currQuestion).find(".likert-scale thead th").not("th:first");
		if(th.length){
			$(th).each(function(i){
				tr += '<td><label class="clean-input-wrap ui-selectee"><input type="radio" class="ui-selectee" name="' + nameid + '"><span class="clean-input ui-selectee"></span></label></td>';
			});
		}

		tr += '</tr>';
		$(tbody).append(tr);
	}

	bindRankEvents();
	refreshAnswersList();

	return true;
}

var sfAddRanking = function(optionLeft, optionRight, type)
{
	var rankHtml = '<li class="ui-sortable-handle"><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="' + optionLeft + '"></div><div class="rank-right"><input type="text" class="ranking-text" value="' + optionRight + '"><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li>';

	$(".text-list").append(rankHtml);
	$("#panelChoices").find(".rank-right a.rank-remove").click(function(){
		sfRemoveRank(this);
	});

	$("#panelChoices").find(".rank-left input.ranking-text").keyup(function(){
		sfChangeLeftOption(this);
	});

	$("#panelChoices").find(".rank-right input.ranking-text").keyup(function(){
		sfChangeRightOption(this);
	});

	var liID = $(currQuestion).attr("id");
	questionsStack[liID].answers.push({left: optionLeft, right: optionRight, leftid: "", rightid: ""});

	if(type == 'ranking-dropdown'){
		var select = $(currQuestion).find(".choices li.ranking-right").get(0);
		select = $(select).html();

		if(select == null){
			select = '<select></select>';
		}

		var rank = '<li class="ranking-left ui-selectee">' + optionLeft + '</li><li class="ranking-right ui-selectee">' + select + '</li><li class="ranking-break ui-selectee"></li>';
		$(currQuestion).find(".choices").append(rank);

		var newOption = '<option value="' + optionRight + '">' + optionRight + '</option>';
		$(currQuestion).find(".choices .ranking-right select").append(newOption);
	}

	if(type == 'ranking-dragdrop'){
		var rank = '<li class="ranking-left fixed ui-selectee">' + optionLeft + '</li><li class="ranking-right dragable ui-selectee ui-widget-header">' + optionRight + '</li><li class="ranking-break ui-selectee"></li>';
		$(currQuestion).find(".choices").append(rank);
		$(".dragable").draggable({containment: $(currQuestion), scroll:false});
	}

	refreshAnswersList();

	return true;
}

var sfAddChoice = function(choiceTitle, addToTools, type)
{
	var choiceTitle = (choiceTitle) ? choiceTitle : 'Choice Text';
	addToTools = (typeof addToTools != 'undefined') ? addToTools : true;

	if(addToTools){
		var choiceHtml = '<li class="ui-sortable-handle" style=""><div class="option"><a href="#" class="remove"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" value="'+choiceTitle+'" class="text"></div></li>';

		$("#choices-list").append(choiceHtml);
		$("#panelChoices").find(".option a").click(function(){
			sfRemoveChoice(this);
		});

		$("#panelChoices").find(".option input").keyup(function(){
			sfChangeChoice(this);
		});
	}

	var liID = $(currQuestion).attr("id");
	questionsStack[liID].answers.push({title: choiceTitle, id: ""});

	var nameID = $(currQuestion).find(".choices").attr("data-id");
	var inputType = (type == 'pick-one') ? 'radio' : 'checkbox';

	var questStyle = questionsStack[liID].choiceStyle;
	if(!questStyle){

		var choice = '<li class="choice ui-selectee"><label class="ui-selectee"><label class="clean-input-wrap ui-selectee"><input type="' + inputType + '" name="' + nameID + '" class="ui-selectee"><span class="clean-input ui-selectee"></span></label> <span class="choice-value ui-selectee">'+choiceTitle+'</span></label></li>';
		
		if($("#" + liID + " .choices .other").length){
			$(choice).insertBefore("#" + liID + " .choices .other");
		} else {
			$(currQuestion).find(".choices").append(choice);
		}

	} else {

		var choice = '<option>' + choiceTitle + '</option>';
		$(currQuestion).find(".choices").append(choice);

	}

	refreshAnswersList();

	return true;
}

var sfCollectQuestions = function(select)
{
	var id = $(select).attr("name").replace("section_", "");
	questionsStack[id].sections = [];

	var options = $(select).find('option:selected');
	if(options.length){
		for(var n = 0; n < options.length; n++){
			questionsStack[id].sections.push($(options[n]).val());
		}
	}
}

var sfSelectOption = function(option)
{
	var select = $(option).parent();
	var options = select.find('option');

	if(options.length){
		for(var n = 0; n < options.length; n++){

			var opt = options[n];
			if($(opt).prop('selected')){
				$(opt).attr("selected", "selected");
			} else {
				$(opt).removeAttr("selected");
			}
		}
	}
}

var sfGetOptions = function(liID)
{
	var qtype = $(currQuestion).attr("name");
	if(!questionsStack[liID].exists){
		questionsStack[liID].exists = 1;
		questionsStack[liID].sf_qdescription = "";
		questionsStack[liID].sf_iscale = "";
		questionsStack[liID].published = 1;
		questionsStack[liID].sf_compulsory = 0;
		questionsStack[liID].sf_default_hided = 0;
		questionsStack[liID].is_final_question = 0;

		switch(qtype){
    		case 'section-separator':
    			questionsStack[liID].sf_qtype = "section-separator";
				questionsStack[liID].sf_qtitle = "Section Heading";
				questionsStack[liID].sections = [];

				var select = '<select multiple="multiple" size="8" name="section_' + liID + '" style="width:90%;" onclick="javascript:sfCollectQuestions(this);">';
				for(var p in questionsStack){

					if(questionsStack[p].sf_qtype !== 'section-separator')
						select += '<option value="' + p + '" onclick="javascript:sfSelectOption(this);">' + questionsStack[p].sf_qtitle + '</option>';
				}

				select += '</select>';

				var phtml = sfGetChoicesPanel();
				phtml = phtml.replace('{PANEL_BODY}', select);
    		break;
    		case 'pick-one':
    			questionsStack[liID].sf_qtype = "pick-one";
				questionsStack[liID].sf_qtitle = "Question Text";
				questionsStack[liID].choiceStyle = 0;
				questionsStack[liID].answers = [];
				questionsStack[liID].answers.push({title: "Choice 1", id: ""});
				questionsStack[liID].answers.push({title: "Choice 2", id: ""});

				var phtml = sfGetChoicesPanel();
				var ohtml = '<ul class="text-list" id="choices-list"><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 1"></div></li><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 2"></div></li></ul>';
				ohtml += sfGetChoicesTools(liID, 'pick-one');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
    		break;
    		case 'pick-many':
    			questionsStack[liID].sf_qtype = "pick-many";
				questionsStack[liID].sf_qtitle = "Question Text";
				questionsStack[liID].choiceStyle = 0;
				questionsStack[liID].answers = [];
				questionsStack[liID].answers.push({title: "Choice 1", id: ""});
				questionsStack[liID].answers.push({title: "Choice 2", id: ""});

				var phtml = sfGetChoicesPanel();
				var ohtml = '<ul class="text-list" id="choices-list"><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 1"></div></li><li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" tabindex="1" value="Choice 2"></div></li></ul>';
				ohtml += sfGetChoicesTools(liID, 'pick-many');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
    		break;
    		case 'short-answer':
    			questionsStack[liID].sf_qtype = "short-answer";
				questionsStack[liID].sf_qtitle = "Every {x} in question text will be replaced by input box. If the number of {x} is more than zero no large text area will be displayed. To place text area with input box in question text use {y} tag.";
				var phtml = '';
    		break;
    		case 'ranking-dropdown':
    			questionsStack[liID].sf_qtype = "ranking-dropdown";
				questionsStack[liID].sf_qtitle = "Question Text";
				questionsStack[liID].answers = [];
				questionsStack[liID].answers.push({left: "Option 1", right: "Rank 1", leftid: "", rightid: ""});
				questionsStack[liID].answers.push({left: "Option 2", right: "Rank 2", leftid: "", rightid: ""});

				var phtml = sfGetChoicesPanel();
				var ohtml = '<ul class="text-list" id="ranking-list"><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 1" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 1" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 2" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 2" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>';
				ohtml += sfGetRankingTools(liID, 'ranking-dropdown');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);

    		break;
    		case 'ranking-dragdrop':
    			questionsStack[liID].sf_qtype = "ranking-dragdrop";
				questionsStack[liID].sf_qtitle = "Question Text";
				questionsStack[liID].answers = [];
				questionsStack[liID].answers.push({left: "Option 1", right: "Rank 1", leftid: "", rightid: ""});
				questionsStack[liID].answers.push({left: "Option 2", right: "Rank 2", leftid: "", rightid: ""});

				var phtml = sfGetChoicesPanel();
				var ohtml = '<ul class="text-list" id="ranking-list"><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 1" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 1" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="Option 2" /></div><div class="rank-right"><input type="text" class="ranking-text" value="Rank 2" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>';
				ohtml += sfGetRankingTools(liID, 'ranking-dragdrop');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
    		break;
    		case 'boilerplate':
    			questionsStack[liID].sf_qtype = "boilerplate";
				questionsStack[liID].sf_qtitle = "Boilerplate";
				var phtml = '';
    		break;
    		case 'ranking':
    		case 'likert-scale':

    			if(qtype == 'ranking'){
    				questionsStack[liID].sf_qtype = "ranking";
					questionsStack[liID].sf_qtitle = "Question Text";
					questionsStack[liID].answers = {ranks: ["1", "2"], options: ["Option 1", "Option 2"], oid: ["", ""], rid: ["", ""]};
				} else if(qtype == 'likert-scale') {
					questionsStack[liID].sf_qtype = "likert-scale";
					questionsStack[liID].sf_qtitle = "Question Text";
					questionsStack[liID].answers = {scales: ["Scale 1", "Scale 2", "Scale 3", "Scale 4"], options: ["Option 1", "Option 2"], oid: ["", ""], sid: ["", "", "", ""]};
				}
				
				var phtml = sfGetChoicesPanel();

				if(qtype == 'ranking'){
					var ohtml = '<ul class="text-list" id="rank-list"><li class="rank-title">Ranks</li><li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" class="rank-text" value="1" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" class="rank-text" value="2" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>';
					ohtml += sfGetRankTools('rank', 'ranking');
				} else if(qtype == 'likert-scale') {
					var ohtml = '<ul class="text-list" id="scale-list"><li class="scale-title">Scales</li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 1" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 2" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 3" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="Scale 4" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>';
					ohtml += sfGetRankTools('rank', 'likert-scale');
				}
				
				if(qtype == 'ranking'){
					var id = 'option-list-ranking';
				} else {
					var id = 'option-list-likert';
				}

				ohtml += '<hr/><ul class="text-list" id="' + id + '"><li class="option-title">Answer Options</li><li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="Option 1" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li><li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="Option 2" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li></ul>';
				if(qtype == 'ranking'){
					ohtml += sfGetRankTools('option', 'ranking');
				} else if(qtype == 'likert-scale') {
					ohtml += sfGetRankTools('option', 'likert-scale');
				}

				phtml = phtml.replace('{PANEL_BODY}', ohtml);
    		break;
    	}    	
	} else {

		switch(qtype){
			case 'section-separator':
				var select = '<select multiple="multiple" size="8" name="section_' + liID + '" style="width:90%;" onclick="javascript:sfCollectQuestions(this);">';
				for(var p in questionsStack){
					if(questionsStack[liID].sections.length){
						if(sf_inArray(questionsStack[liID].sections, p))
							selected = 'selected="selected"';
						else
							selected = '';
					} else {
						selected = '';
					}

					if(questionsStack[p].sf_qtype !== 'section-separator')
						select += '<option value="' + p + '" ' + selected +' onclick="javascript:sfSelectOption(this);">' + questionsStack[p].sf_qtitle + '</option>';
				}

				select += '</select>';

				var phtml = sfGetChoicesPanel();
				phtml = phtml.replace('{PANEL_BODY}', select);
			break;
			case 'pick-one':
				var phtml = sfGetChoicesPanel();
				var ohtml = sfGetChoices(liID, 'pick-one');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
			break;
			case 'pick-many':
				var phtml = sfGetChoicesPanel();
				var ohtml = sfGetChoices(liID, 'pick-many');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
			break;
			case 'ranking-dropdown':
				var phtml = sfGetChoicesPanel();
				var ohtml = sfGetRanking(liID, 'ranking-dropdown');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
			break;
			case 'ranking-dragdrop':
				var phtml = sfGetChoicesPanel();
				var ohtml = sfGetRanking(liID, 'ranking-dragdrop');
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
			break;
			case 'ranking':
			case 'likert-scale':
				var phtml = sfGetChoicesPanel();
				if(qtype == 'ranking'){
					var ohtml = sfGetRanking(liID, 'ranking');
				} else if(qtype == 'likert-scale') {
					var ohtml = sfGetRanking(liID, 'likert-scale');
				}
				phtml = phtml.replace('{PANEL_BODY}', ohtml);
			break;
		}

	}

	sfRemoveChoicesPanel();
	sfSetOptionsFields(liID);
	sfEnableOptions();
    sfAddQuestionOption(phtml);

	$(".tab-pane").removeClass("active");
	$(".nav-tabs li").removeClass("active");
	$("#optionsButton").addClass("active");
	$("#options").addClass("active");

	$(".dragable").draggable({containment: $(currQuestion), scroll:false});
	refreshQuestionsList();
	refreshAnswersList();
}

var sfChangeScale = function(elem)
{
	var liID = $(currQuestion).attr("id");
	var newText = $(elem).val();
	var index = $(elem).parent().parent().index();
	
	questionsStack[liID].answers.scales[index - 1] = newText;
	var th = $(currQuestion).find(".likert-scale th").get(index);
	$(th).html(newText);

	refreshAnswersList();
	changeAnswersInHides(index - 1);

	return true;
}

var sfChangeRank = function(elem)
{
	var liID = $(currQuestion).attr("id");
	var newText = $(elem).val();
	var index = $(elem).parent().parent().index();
	index--;
	
	questionsStack[liID].answers.ranks[index] = newText;
	var selects = $(currQuestion).find(".choices .ranking-right select");
	$(selects).each(function(i){
		var options = $(selects[i]).find("option");
		var option = $(options).get(index);
		$(option).val(newText);
		$(option).text(newText);
	});

	refreshAnswersList();
	changeAnswersInHides(index);

	return true;
}

var sfChangeOption = function(elem, qtype)
{
	var liID = $(currQuestion).attr("id");
	var newText = $(elem).val();
	var index = $(elem).parent().parent().index();
	index--;

	questionsStack[liID].answers.options[index] = newText;
	if(qtype == 'ranking'){
		var options = $(currQuestion).find(".choices .ranking-left");
		var option = $(options).get(index);
		$(option).html(newText);
	} else if(qtype == 'likert-scale') {
		var tr = $(currQuestion).find(".likert-scale tr").get(index + 1);
		$(tr).find("td:first").html(newText);
	}

	refreshAnswersList();

	return true;
}

var sfGetRankTools = function(type, qtype){

	switch(type){
		case 'rank':
			var func = 'sfAddRank(\'' + qtype + '\')';
		break;
		case 'option':
			var func = 'sfAddOption(\'' + qtype + '\')';
		break;
	}

	ohtml = '<hr/><div class="choices-toolbar option-toolbar"><a class="toolbox-action button button-small" onclick="javascript:' + func + ';"><i class="fa fa-plus-circle">&nbsp;Add</i></a></div>';

	return ohtml;
}

var sfGetRankingTools = function(liID, type)
{
	switch(type){
		case 'ranking-dropdown':
			addFunc = 'javascript:sfAddRanking(\'Option Text\', \'Rank Text\', \'ranking-dropdown\');';
		break;
		case 'ranking-dragdrop':
			addFunc = 'javascript:sfAddRanking(\'Option Text\', \'Rank Text\', \'ranking-dragdrop\');';
		break;
	}

	var tools = '<hr/><div class="choices-toolbar ranking-toolbar"><a class="toolbox-action button button-small" onclick="' + addFunc + '"><i class="fa fa-plus-circle">&nbsp;Add</i></a></div>';
	return tools;
}

var sfGetChoicesTools = function(liID, type)
{
	if(questionsStack[liID].answers[0]['other_option'])
	{
		var checked = 'checked="checked"';
		var otherText = questionsStack[liID].answers[0]['other_option_text'];
	} else {
		var checked = '';
		var otherText = '';
	}

	var selected1 = '';
	var selected2 = '';
	if(!questionsStack[liID].choiceStyle){
		selected1 = "selected='selected'";
	} else {
		selected2 = "selected='selected'";
	}

	switch(type){
		case 'pick-one':
			addFunc = 'javascript:sfAddChoice(\'Choice Text\', true, \'pick-one\');';
		break;
		case 'pick-many':
			addFunc = 'javascript:sfAddChoice(\'Choice Text\', true, \'pick-many\');';
		break;
	}

	var tools = '<hr/><div class="choices-toolbar"><a class="toolbox-action button button-small" onclick="' + addFunc + '"><i class="fa fa-plus-circle">&nbsp;Add</i></a><a class="toolbox-action button button-small" onclick="javascript:sfDialogBulkList(\'' + type + '\');"><i class="fa fa-list">&nbsp;Bulk</i></a></div><br/><ul class="text-list"><li id="sf_other"><input type="checkbox" name="other_option_cb" id="other_option_cb" class="css-checkbox" onclick="javascript:sfToggleOtherOption(\'' + type + '\');" ' +checked +'/><label class="css-label cb0" for="other_option_cb">Others option</label><input type="text" name="other_option" id="other_option" class="input-large" placeholder="Others option" onkeyup="javascript:sfChangeOtherOption();" value="' + otherText + '"/></li>';

	if(type == 'pick-one'){
		tools += '<li><label for="sf_qstyle" class="use-dropdown">Use drop-down style:</label><select data-style="btn" id="sf_qstyle" name="sf_qstyle" class="form-control" onchange="javascript:sfChangeStyle(this)"><option value="0" '+selected1+'>No</option><option value="1" '+selected2+'>Yes</option></select></li>';
	}

	tools += '</ul>';

	return tools;
}

var sfChangeStyle = function(select)
{
	var liID = $(currQuestion).attr("id");
	var style = $(select).val();
	var addToTools = false;

	if(style == 0){

		questionsStack[liID].choiceStyle = 0;
		dataId = $(currQuestion).find(".choices").attr("data-id");
		var chtml = '<ul class="choices" data-id="'+dataId+'"></ul>';
		$(currQuestion).find(".choices").remove();
		$(currQuestion).append(chtml);

		if(questionsStack[liID].answers.length){
			var tmp_answers = sfClone(questionsStack[liID]);
			
			delete(questionsStack[liID].answers);
			questionsStack[liID].answers = [];
			for(var n = 0; n < tmp_answers.answers.length; n++){
				var choiceTitle = tmp_answers.answers[n].title;
				sfAddChoice(choiceTitle, addToTools, 'pick-one');
			}
		}
	}

	if(style == 1){
		questionsStack[liID].choiceStyle = 1;
		sfAddToDropDown();
	}

	return true;
}

var sfAddToDropDown = function(){

	dataId = $(currQuestion).find(".choices").attr("data-id");
	$(currQuestion).find(".choices").remove();
	var liID = $(currQuestion).attr("id");
	var answ = questionsStack[liID].answers;
	shtml = '<select class="choices" data-id="'+dataId+'">';
	if(answ.length)
		for(var i = 0; i < answ.length; i++){
			shtml += '<option>' + answ[i].title + '</option>';
		}

	shtml += '</select>';

	$(currQuestion).append(shtml);

	return true;
}

var sfGetRanking = function(liID, type)
{
	if(type == 'ranking'){
		var ohtml = '<ul class="text-list" id="rank-list"><li class="rank-title">Ranks</li>';
		var ranks = questionsStack[liID].answers['ranks'];
		if(ranks.length){
			for(var n=0;n < ranks.length; n++){
				ohtml += '<li><div class="rank"><i class="fa fa-arrows rank-order"></i><input type="text" class="rank-text" value="' + ranks[n] + '" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
			}
		}

		ohtml += '</ul>';
		ohtml += sfGetRankTools('rank', type);
		ohtml += '<hr/><ul class="text-list" id="option-list-ranking"><li class="option-title">Answer Options</li>';
		var options = questionsStack[liID].answers['options'];

		if(options.length){
			for(var n = 0; n < options.length; n++){
				ohtml += '<li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="' + options[n] + '" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
			}
		}
		ohtml += '</ul>';
		ohtml += sfGetRankTools('option', type);

	} else if(type == 'likert-scale'){
		var ohtml = '<ul class="text-list" id="scale-list"><li class="scale-title">Scales</li>';
		var scales = questionsStack[liID].answers['scales'];
		if(scales.length){
			for(var n=0;n < scales.length; n++){
				ohtml += '<li><div class="scale"><i class="fa fa-arrows scale-order"></i><input type="text" class="scale-text" value="' + scales[n] + '" /><a class="remove scale-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
			}
		}

		ohtml += '</ul>';
		ohtml += sfGetRankTools('rank', type);
		ohtml += '<hr/><ul class="text-list" id="option-list-likert"><li class="option-title">Answer Options</li>';
		var options = questionsStack[liID].answers['options'];

		if(options.length){
			for(var n = 0; n < options.length; n++){
				ohtml += '<li><div class="options"><i class="fa fa-arrows option-order"></i><input type="text" class="option-text" value="' + options[n] + '" /><a class="remove option-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
			}
		}
		ohtml += '</ul>';
		ohtml += sfGetRankTools('option', type);
	} else {
		var ohtml = '<ul class="text-list" id="ranking-list">';
		var rankings = questionsStack[liID].answers;
		if(rankings.length)
			for(var n = 0; n < rankings.length; n++){
				ohtml += '<li><div class="rank-left"><i class="fa fa-arrows rank-order"></i><input type="text" class="ranking-text" value="' + rankings[n].left + '" /></div><div class="rank-right"><input type="text" class="ranking-text" value="' + rankings[n].right + '" /><a class="remove rank-remove" href="#"><i class="fa fa-times"></i></a></div></li>';
			}

		ohtml += '</ul>';
		ohtml += sfGetRankingTools(liID, type);
	}

	return ohtml;
}

var sfGetChoices = function(liID, type)
{
	var ohtml = '<ul class="text-list" id="choices-list">';
	var choices = questionsStack[liID].answers;
	if(choices.length)
		for(var n = 0; n < choices.length; n++){
			ohtml += '<li><div class="option"><a class="remove" href="#"><i class="fa fa-times"></i></a><i class="fa fa-arrows"></i><input type="text" class="text" value="' + choices[n].title + '"></div></li>';
		}

	ohtml += '</ul>';
	ohtml += sfGetChoicesTools(liID, type);

	return ohtml;
}

var sfToggleOtherOption = function(type)
{
	var liID = $(currQuestion).attr("id");
	var nameID = $("#" + liID).find("ul.choices").attr("data-id");
	if($("#other_option_cb").prop("checked")){

		$("#other_option").val('Other, please specify...');

		if(type == 'pick-one'){
			var otherOption = '<li class="choice other"><label><label class="clean-input-wrap"><input type="radio" name="'+nameID+'"><span class="clean-input"></span></label> <span class="choice-value">Other, please specify...</span><input type="text" "="" class="text dummy"></label></li>';
		}

		if(type == 'pick-many'){
			var otherOption = '<li class="choice other"><label><label class="clean-input-wrap"><input type="checkbox" name="'+nameID+'"><span class="clean-input"></span></label> <span class="choice-value">Other, please specify...</span><input type="text" "="" class="text dummy"></label></li>';
		}

		$("#" + liID + " .choices").append(otherOption);

		questionsStack[liID].answers[0]['other_option'] = 1;
		questionsStack[liID].answers[0]['other_option_text'] = 'Other, please specify...';
	} else {
		$("#" + liID).find("li.other").remove();
		questionsStack[liID].answers[0]['other_option'] = 0;
		questionsStack[liID].answers[0]['other_option_text'] = '';
		$("#other_option").val('');
	}

	return true;
}

var sfChangeOtherOption = function()
{
	var liID = $(currQuestion).attr("id");
	var otherText = $("#other_option").val();

	$(currQuestion).find("ul.choices .other .choice-value").text(otherText);
	questionsStack[liID].answers[0]['other_option_text'] = otherText;

	return true;
}

var sfRemoveChoicesPanel = function()
{
	$("#panelChoices").remove();
	return true;
}

var sfStartIndex = function(event, ui)
{
	var dragIndex = $(ui.item).index();
	return dragIndex;
}

var sfChangeScaleOrdering = function(event, ui, start_index)
{
	var liID = $(currQuestion).attr("id");
	var prevIndex = $(ui.item).prev().index();
	var nextIndex = $(ui.item).next().index();

	var hDrag = $(currQuestion).find(".likert-scale thead th").get(start_index);
	var hTr = $(currQuestion).find(".likert-scale thead tr");
	var tr = $(currQuestion).find(".likert-scale tbody tr");
	if(prevIndex >= 0){
		if(start_index > prevIndex){
			var prevElem = $(hTr).find("th").get(prevIndex);
			$(hDrag).insertAfter(prevElem);
		}

		if(start_index <= prevIndex){
			var prevElem = $(hTr).find("th").get(prevIndex + 1);
			$(hDrag).insertAfter(prevElem);
		}
	} else {
		var nextElem = $(hTr).find("th").get(nextIndex - 1);
		$(hDrag).insertBefore(nextElem);
	}

	if(tr.length){
		$(tr).each(function(n){
			var tDrag = $(tr[n]).find("td").get(start_index);
			if(prevIndex >= 0){
				if(start_index > prevIndex){
					var prevElem = $(tr[n]).find("td").get(prevIndex);
					$(tDrag).insertAfter(prevElem);
				}

				if(start_index <= prevIndex){
					var prevElem = $(tr[n]).find("td").get(prevIndex + 1);
					$(tDrag).insertAfter(prevElem);
				}
			} else {
				var nextElem = $(tr[n]).find("td").get(nextIndex - 1);
				$(tDrag).insertBefore(nextElem);
			}
		});
	}

	var scales = $("#scale-list .scale .scale-text");
	$(scales).each(function(index){
		var value = $(scales[index]).val();
		questionsStack[liID].answers.scales[index] = value;
	});

	refreshAnswersList();
}

var sfChangeOptionOrdering = function(event, ui, start_index, qtype)
{
	if($(ui).hasClass("option-title")){
		return false;
	}

	var liID = $(currQuestion).attr("id");
	var prevIndex = $(ui.item).prev().index();
	var nextIndex = $(ui.item).next().index();

	prevIndex--;
	nextIndex--;
	start_index--;

	if(qtype == 'ranking'){
		var oLeft = $(currQuestion).find(".choices .ranking-left");
		var oRight = $(currQuestion).find(".choices .ranking-right");
		var oBreak = $(currQuestion).find(".choices .ranking-break");
		var dLeft = $(oLeft).get(start_index);
		var dRight = $(oRight).get(start_index);
		var dBreak = $(oBreak).get(start_index);

		if(prevIndex >= 0){
			if(start_index > prevIndex){
				var prevElem = $(oBreak).get(prevIndex);
				$(dBreak).insertAfter(prevElem);
				$(dRight).insertAfter(prevElem);
				$(dLeft).insertAfter(prevElem);			
			}
			if(start_index <= prevIndex){
				var prevElem = $(oBreak).get(prevIndex + 1);
				$(dBreak).insertAfter(prevElem);
				$(dRight).insertAfter(prevElem);
				$(dLeft).insertAfter(prevElem);			
			}
		} else {
			var nextElem = $(oLeft).get(nextIndex - 1);
			$(dLeft).insertBefore(nextElem);
			$(dRight).insertBefore(nextElem);
			$(dBreak).insertBefore(nextElem);
				
		}

		qRank = $("#rank-list .rank .rank-text");
		qOption = $("#option-list-ranking .options .option-text");

		$(qRank).each(function(index){
			var vRank = $(qRank[index]).val();
			questionsStack[liID].answers.ranks[index] = vRank;
		});

		$(qOption).each(function(index){
			var vOption = $(qOption[index]).val();
			questionsStack[liID].answers.options[index] = vOption;
		});
	} else if(qtype == 'likert-scale') {

		dTr = $(currQuestion).find(".likert-scale tbody tr").get(start_index);
		if(prevIndex >= 0){
			if(start_index > prevIndex){
				var prevElem = $(currQuestion).find(".likert-scale tbody tr").get(prevIndex);
				$(dTr).insertAfter(prevElem);
			}

			if(start_index <= prevIndex){
				var prevElem = $(currQuestion).find(".likert-scale tbody tr").get(prevIndex + 1);
				$(dTr).insertAfter(prevElem);
			}

		} else {
			var nextElem = $(currQuestion).find(".likert-scale tbody tr").get(nextIndex - 1);
			$(dTr).insertBefore(nextElem);
		}

		qOption = $("#option-list-likert .options .option-text");
		$(qOption).each(function(index){
			var vOption = $(qOption[index]).val();
			questionsStack[liID].answers.options[index] = vOption;
		});
	}

	refreshAnswersList();

	return true;
}

//sfChangeRankOrdering2 for rank ordering in ranking questions
var sfChangeRankOrdering2 = function(event, ui, start_index)
{
	if($(ui).hasClass("rank-title")){
		return false;
	}

	var liID = $(currQuestion).attr("id");
	var selects = $(currQuestion).find(".choices .ranking-right select");

	var prevIndex = $(ui.item).prev().index();
	var nextIndex = $(ui.item).next().index();

	prevIndex--;
	nextIndex--;
	start_index--;
	
	$(selects).each(function(i){
		var options = $(selects[i]).find("option");
		var dOption = $(options).get(start_index);

		if(prevIndex >= 0){
			if(start_index > prevIndex){
				var prevElem = $(options).get(prevIndex);
				$(dOption).insertAfter(prevElem);
			}
			if(start_index <= prevIndex){
				var prevElem = $(options).get(prevIndex + 1);
				$(dOption).insertAfter(prevElem);
			}

		} else {
			var nextElem = $(options).get(nextIndex - 1);
			$(dOption).insertBefore(nextElem);
		}

	});
	
	ranks = $("#rank-list div.rank .rank-text");
	$(ranks).each(function(index){
		var new_value = $(ranks[index]).val();
		questionsStack[liID].answers.ranks[index] = new_value;
	});

	refreshAnswersList();

	return true;

}

var sfChangeRankOrdering = function( event, ui, start_index )
{
	var liID = $(currQuestion).attr("id");
	var rLeft = $(currQuestion).find(".choices li.ranking-left");
	var rRight = $(currQuestion).find(".choices li.ranking-right");
	var rBreak = $(currQuestion).find(".choices li.ranking-break");
	var dLeft = $(rLeft).get(start_index);
	var dRight = $(rRight).get(start_index);
	var dBreak = $(rBreak).get(start_index);

	var prevIndex = $(ui.item).prev().index();
	var nextIndex = $(ui.item).next().index();

	if(prevIndex >= 0){
		if(start_index > prevIndex){
			var prevElem = $(rBreak).get(prevIndex);
			$(dBreak).insertAfter(prevElem);
			$(dRight).insertAfter(prevElem);
			$(dLeft).insertAfter(prevElem);			
		}
		if(start_index <= prevIndex){
			var prevElem = $(rBreak).get(prevIndex + 1);
			$(dBreak).insertAfter(prevElem);
			$(dRight).insertAfter(prevElem);
			$(dLeft).insertAfter(prevElem);			
		}
	} else {
		var nextElem = $(rLeft).get(nextIndex - 1);
		$(dLeft).insertBefore(nextElem);
		$(dRight).insertBefore(nextElem);
		$(dBreak).insertBefore(nextElem);
			
	}

	qLeft = $("#ranking-list li div.rank-left .ranking-text");
	qRight = $("#ranking-list li div.rank-right .ranking-text");

	$(qLeft).each(function(index){
		var vLeft = $(qLeft[index]).val();
		var vRight = $(qRight[index]).val();

		questionsStack[liID].answers[index]['left'] = vLeft;
		questionsStack[liID].answers[index]['right'] = vRight;
	});

	refreshAnswersList();
}

var sfChangeChoiceOrdering = function(event, ui, start_index)
{
	var liID = $(currQuestion).attr("id");
	
	var questStyle = questionsStack[liID].choiceStyle;
	var quests = (!questStyle) ? $(currQuestion).find(".choices li") : $(currQuestion).find(".choices option");
	var dragElem = $(quests).get(start_index);
	var prevIndex = $(ui.item).prev().index();
	var nextIndex = $(ui.item).next().index();

	if(prevIndex >= 0){
		
		if(start_index > prevIndex){
			var prevElem = $(quests).get(prevIndex);
			$(dragElem).insertAfter(prevElem);
		}
		if(start_index <= prevIndex){
			var prevElem = $(quests).get(prevIndex + 1);
			$(dragElem).insertAfter(prevElem);
		}

	} else {
		var nextElem = $(quests).get(nextIndex - 1);
		$(dragElem).insertBefore(nextElem);
	}

	quests = (!questStyle) ? $(currQuestion).find(".choices li") : $(currQuestion).find(".choices option");
	$(quests).each(function(index){
		if(!questStyle){
			var new_value = $(quests[index]).find(".choice-value").text();
		} else {
			var new_value = $(quests[index]).text();
		}

		questionsStack[liID].answers[index]['title'] = new_value;
	});
	
	refreshAnswersList();

	return true;
}

var sfInsertQuestion = function(qhtml, liID)
{
	$(".placeholder").remove();
	$("#survey-questions" + currPage).append(qhtml);

	sfClearActives();
	$("#" + liID).addClass("active");
	$("#" + liID + " i.remove").css("opacity", "1");
	$("#survey-questions" + currPage).selectable();

	$("#" + liID).click(function(){
		sfClearActives();
		$(this).addClass("active");
		$(this).find("i.remove").css("opacity", "1");
		currQuestion = $(this);
		sfGetOptions(liID);
		return true;
	});

	currQuestion = $("#" + liID);
	questionsStack[liID] = {};
	questionsStack[liID].exists = 0;
	questionsStack[liID].page = currPage;
	questionsStack[liID].hides = [];
	questionsStack[liID].rules = [];
	questionsStack[liID].questOrdering = '';
	sfGetOptions(liID);
	
	questOrdering++;
	sf_SortQuestions();
	return true;
}

var sfClearActives = function(){
	$("ol.page li").removeClass("active");
	$("ol.page i.remove").css("opacity", "0.2");
}

var refreshQuestionsList = function()
{
	var liID = $(currQuestion).attr("id");
	if(questionsStack){

		var questionList2 = $("#sf_quest_list2");
		questionList2.html('');

		var questionList3 = $("#sf_quest_list3");
		questionList3.html('');
		
		var questionList = $("#sf_quest_list");
		questionList.html('');
		var option = '<option value="0">- Select question -</option>';

		for(var id in questionsStack){

			if(id == liID){
				continue;
			}

			if(questionsStack[id].sf_qtype != 'short-answer' && questionsStack[id].sf_qtype != 'boilerplate' && questionsStack[id].sf_qtype != 'section-separator' && questionsStack[id].sf_qtype != 'page-break'){

				var title = (questionsStack[id].sf_qtitle.length > 30) ? questionsStack[id].sf_qtitle.substr(0, 30) + "..." : questionsStack[id].sf_qtitle;
				option += '<option value="' + id + '">' + title + '</option>';

			}
		}

		questionList2.html(option);
		questionList3.html(option);
		questionList.html(option);
	}
}

var refreshAnswersList = function()
{
	if(currQuestion){
		var isOption = false;
		var liID = $(currQuestion).attr("id");
		var answer = '<option value="0">- Select answer -</option>';
		
		var sf_field_list = $("#sf_field_list");
		var sf_option_list = $("#sf_option_list");
		sf_field_list.html('');
		sf_option_list.html('');

		if(liID){
			var qtype = questionsStack[liID].sf_qtype;
			switch (qtype){
				case 'pick-one':
				case 'pick-many':
					if(questionsStack[liID].answers.length)
					for(var n = 0; n < questionsStack[liID].answers.length; n++){
						var ans = (questionsStack[liID].answers[n].title.length > 30) ? questionsStack[liID].answers[n].title.substr(0, 30) + "..." : questionsStack[liID].answers[n].title;
						answer += '<option value="' + (n + 1) + '">' + ans + '</option>';
					}
				break;
				case 'ranking-dropdown':
				case 'ranking-dragdrop':
					isOption = true;
					option = '<option value="0">- Select option -</option>';

					if(questionsStack[liID].answers.length)
					for(var n = 0; n < questionsStack[liID].answers.length; n++){
						var right = (questionsStack[liID].answers[n].right.length > 30) ? questionsStack[liID].answers[n].right.substr(0, 30) + "..." : questionsStack[liID].answers[n].right;
						var left = (questionsStack[liID].answers[n].left.length > 30) ? questionsStack[liID].answers[n].left.substr(0, 30) + "..." : questionsStack[liID].answers[n].left;

						answer += '<option value="' + (n + 1) + '">' + right + '</option>';
						option += '<option value="' + (n + 1) + '">' + left + '</option>';
					}
				break;
				case 'ranking':
					isOption = true;
					option = '<option value="0">- Select option -</option>';

					if(questionsStack[liID].answers.ranks.length)
					for(var n = 0; n < questionsStack[liID].answers.ranks.length; n++){
						var right = (questionsStack[liID].answers.ranks[n].length > 30) ? questionsStack[liID].answers.ranks[n].substr(0, 30) + "..." : questionsStack[liID].answers.ranks[n];
						
						answer += '<option value="' + (n + 1) + '">' + right + '</option>';
					}

					if(questionsStack[liID].answers.options.length)
					for(var n = 0; n < questionsStack[liID].answers.options.length; n++){
						var left = (questionsStack[liID].answers.options[n].length > 30) ? questionsStack[liID].answers.options[n].substr(0, 30) + "..." : questionsStack[liID].answers.options[n];

						option += '<option value="' + (n + 1) + '">' + left + '</option>';
					}	
				break;
				case 'likert-scale':
					isOption = true;
					option = '<option value="0">- Select option -</option>';

					if(questionsStack[liID].answers.scales.length)
					for(var n = 0; n < questionsStack[liID].answers.scales.length; n++){
						var right = (questionsStack[liID].answers.scales[n].length > 30) ? questionsStack[liID].answers.scales[n].substr(0, 30) + "..." : questionsStack[liID].answers.scales[n];
						
						answer += '<option value="' + (n + 1) + '">' + right + '</option>';
					}

					if(questionsStack[liID].answers.options.length)
					for(var n = 0; n < questionsStack[liID].answers.options.length; n++){
						var left = (questionsStack[liID].answers.options[n].length > 30) ? questionsStack[liID].answers.options[n].substr(0, 30) + "..." : questionsStack[liID].answers.options[n];

						option += '<option value="' + (n + 1) + '">' + left + '</option>';
					}
				break;
			}
			
		}

		sf_field_list.html(answer);
		if(isOption){
			$(".rule_option").show();
			sf_option_list.html(option);
		} else {
			$(".rule_option").hide();
			sf_option_list.html('');
		}

		getHideQuestion();
		getRulesQuestion();
		sfGetAnswers($("#sf_quest_list3"));
	}

	return true;
}

var sfGetAnswers = function(elem)
{
	$("#hide_for_option").next().remove();
	$("#hide_for_option").remove();
	var f_scale_data = $("#f_scale_data");
	f_scale_data.html('');

	var id = $(elem).val();
	if(id != '0'){
		var option = '<option value="0">- Select answer -</option>';
		var qtype = questionsStack[id].sf_qtype;

		switch (qtype){
			case 'pick-one':
			case 'pick-many':
				if(questionsStack[id].answers.length)
				for(var n = 0; n < questionsStack[id].answers.length; n++){
					var answer = (questionsStack[id].answers[n].title.length > 30) ? questionsStack[id].answers[n].title.substr(0, 30) + "..." : questionsStack[id].answers[n].title;
					option += '<option value="' + (n + 1) + '">' + answer + '</option>';
				}
			break;
			case 'ranking-dropdown':
			case 'ranking-dragdrop':
				var for_option = '<div class="control-group form-inline" id="hide_for_option"><label class="control-label">And for option:</label><div class="controls"><select id="sf_field_data_m" name="sf_field_data_m" style="width:250px;"></select></div></div><div style="clear:both"><br/></div>';
				var left_option = '<option value="0">- Select option -</option>';

				if(questionsStack[id].answers.length)
					for(var n = 0; n < questionsStack[id].answers.length; n++){
						var left = (questionsStack[id].answers[n].left.length > 30) ? questionsStack[id].answers[n].left.substr(0, 30) + "..." : questionsStack[id].answers[n].left;
						left_option += '<option value="' + (n + 1) + '">' + left + '</option>';
					}
				$(for_option).insertAfter($("#hide_for_question"));
				$("#sf_field_data_m").html(left_option)

				if(questionsStack[id].answers.length)
				for(var n = 0; n < questionsStack[id].answers.length; n++){
					var right = (questionsStack[id].answers[n].right.length > 30) ? questionsStack[id].answers[n].right.substr(0, 30) + "..." : questionsStack[id].answers[n].right;
					option += '<option value="' + (n + 1) + '">' + right + '</option>';
				}

			break;
			case 'ranking':
			case 'likert-scale':
				var for_option = '<div class="control-group form-inline" id="hide_for_option"><label class="control-label">And for option:</label><div class="controls"><select id="sf_field_data_m" name="sf_field_data_m" style="width:250px;"></select></div></div><div style="clear:both"><br/></div>';
				var left_option = '<option value="0">- Select option -</option>';
				if(questionsStack[id].answers.options.length)
					for(var n = 0; n < questionsStack[id].answers.options.length; n++){
						var left = (questionsStack[id].answers.options[n].length > 30) ? questionsStack[id].answers.options[n].substr(0, 30) + "..." : questionsStack[id].answers.options[n];
						left_option += '<option value="' + (n + 1) + '">' + left + '</option>';
					}
				
				$(for_option).insertAfter($("#hide_for_question"));
				$("#sf_field_data_m").html(left_option)

				if(qtype == 'ranking'){
					if(questionsStack[id].answers.ranks.length)
					for(var n = 0; n < questionsStack[id].answers.ranks.length; n++){
						var right = (questionsStack[id].answers.ranks[n].length > 30) ? questionsStack[id].answers.ranks[n].substr(0, 30) + "..." : questionsStack[id].answers.ranks[n];
						option += '<option value="' + (n + 1) + '">' + right + '</option>';
					}
				} else if(qtype == 'likert-scale'){
					if(questionsStack[id].answers.scales.length)
					for(var n = 0; n < questionsStack[id].answers.scales.length; n++){
						var right = (questionsStack[id].answers.scales[n].length > 30) ? questionsStack[id].answers.scales[n].substr(0, 30) + "..." : questionsStack[id].answers.scales[n];
						option += '<option value="' + (n + 1) + '">' + right + '</option>';
					}
				}

			break;
		}

		f_scale_data.html(option);
	}

	return true;
}

var sfAddHideQuestion = function()
{
	var tbody = $("#show_quest tbody");
	if(!tbody){
		tbody = '<tbody><tr id="title"><th colspan="4" class="title">Hide this question if:</th></tr></tbody>';
		$("#show_quest").append(tbody);
	}

	var liID = $(currQuestion).attr("id");
	var sf_quest_list3 = $("#sf_quest_list3").val();
	var sf_field_data_m = $("#sf_field_data_m").val();
	var f_scale_data = $("#f_scale_data").val();

	if(sf_quest_list3 == '0' || f_scale_data == '0'){
		alert("Select question and answer!");
		return false;
	}

	var qtype = questionsStack[liID].sf_qtype;
	if(qtype == 'ranking-dragdrop' || qtype == 'ranking-dropdown' || qtype == 'ranking' || qtype == 'likert-scale'){
		if(sf_field_data_m == '0'){
			alert("Select option please!");
			return false;
		}
	}

	var quest_list3 = $("#sf_quest_list3 option:selected").text();
	var field_data_m = $("#sf_field_data_m option:selected").text();
	var scale_data = $("#f_scale_data option:selected").text();

	var tr = '<tr><td class="hide_'+liID+'">' + quest_list3 + '</td><td>' + field_data_m + '</td><td class="ans_'+liID+'_'+(f_scale_data - 1)+'">' + scale_data + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
	$(tbody).append(tr);

	questionsStack[liID].hides.push({qtype: qtype, question: sf_quest_list3, option: sf_field_data_m, answer: f_scale_data});
	return true;
}

var getHideQuestion = function()
{
	if(currQuestion){
		var liID = $(currQuestion).attr("id");
		var trs = $("#show_quest tbody tr").not("#title");
		trs.html('');
		var tbody = $("#show_quest tbody");

		var hides = questionsStack[liID].hides;
		var tr = '<tr id="title"><th colspan="4" class="title">Hide this question if:</th></tr>';
		if(currQuestion && hides.length){
			for (var n = 0; n < hides.length; n++) {

				if(!questionsStack[hides[n].question]){
					questionsStack[liID].hides.splice(n, 1);
					continue;
				}

				var title = questionsStack[hides[n].question].sf_qtitle;
				title = (title.length >= 30) ? title.substr(0, 30)+"..." : title;
				var qtype = questionsStack[hides[n].question].sf_qtype;

				switch(qtype){
					case 'pick-one'	:
					case 'pick-many':
						var ans = hides[n].answer - 1;
						scale_data = questionsStack[hides[n].question].answers[ans].title;
						var tr = tr + '<tr><td class="hide_' + hides[n].question + '">' + title + '</td><td>&nbsp;</td><td class="answ_' + hides[n].question + '_' + ans + '">' + scale_data + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
					break;
					case 'ranking-dropdown':
					case 'ranking-dragdrop':
						var ans = hides[n].answer - 1;
						var right = questionsStack[hides[n].question].answers[ans].right;
						var left = questionsStack[hides[n].question].answers[ans].left;
						var tr = tr + '<tr><td class="hide_' + hides[n].question + '">' + title + '</td><td>'+ left +'</td><td class="answ_' + hides[n].question + '_' + ans + '">' + right + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
					break;
					case 'ranking':
						var ans = hides[n].answer - 1;
						var rank = questionsStack[hides[n].question].answers.ranks[ans];
						var option = questionsStack[hides[n].question].answers.options[ans];
						var tr = tr + '<tr><td class="hide_' + hides[n].question + '">' + title + '</td><td>'+ option +'</td><td class="answ_' + hides[n].question + '_' + ans + '">' + rank + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
					break;
					case 'likert-scale':
						var ans = hides[n].answer - 1;
						var scale = questionsStack[hides[n].question].answers.scales[ans];
						var option = questionsStack[hides[n].question].answers.options[ans];
						var tr = tr + '<tr><td class="hide_' + hides[n].question + '">' + title + '</td><td>'+ option +'</td><td class="answ_' + hides[n].question + '_' + ans + '">' + scale + '</td><td><a class="remove hide-remove" href="#" onclick="removeHide(this);"><i class="fa fa-times"></i></a></td></tr>';
					break;
				}
			}

			tbody.html(tr);
		}
	}

	return true;
}

var changeQuestionInHides = function()
{
	var liID = $(currQuestion).attr("id");
	var title = questionsStack[liID].sf_qtitle;

	$(".hide_" + liID).text(title);
	$(".rule_" + liID).text(title);
}

var changeAnswersInHides = function(n)
{
	var liID = $(currQuestion).attr("id");
	var qtype = questionsStack[liID].sf_qtype;

	switch(qtype){
		case 'pick-one':
		case 'pick-many':
			var answer = questionsStack[liID].answers[n].title;
		break;
		case 'ranking-dropdown':
		case 'ranking-dragdrop':
			var answer = questionsStack[liID].answers[n].right;
		break;
		case 'ranking':
			var answer = questionsStack[liID].answers.ranks[n];
		break;
		case 'likert-scale':
			var answer = questionsStack[liID].answers.scales[n];
		break;
	}

	$(".answ_" + liID + '_' + n).text(answer);
	$(".rule_answ_" + liID + '_' + n).text(answer);
}

var removeAnswersInHides = function(n)
{
	var liID = $(currQuestion).attr("id");
	$(".answ_" + liID + '_' + n).parent().remove();	
}

var removeHide = function(elem)
{
	var liID = $(currQuestion).attr("id");
	var tr = $(elem).parent().parent();

	var index = $(tr).index();
	questionsStack[liID].hides.splice(index - 1, 1);

	$(tr).fadeOut(300, function(){
		$(tr).remove();
	});
}

var sfAddQuestionRule = function()
{
	var qfld_tbl_rule = $("#qfld_tbl_rule tbody");

	var liID = $(currQuestion).attr("id");
	var qtype = questionsStack[liID].sf_qtype;

	if(qtype == 'short-answer' || qtype == 'boilerplate'){
		return true;
	}

	var sf_field_list = $("#sf_field_list").val();
	var sf_quest_list = $("#sf_quest_list").val();
	var new_priority = $("#new_priority").val();
	var new_option = $("#sf_option_list").val();

	if(sf_field_list == '0'){
		alert("Select answer please!");
		return false;
	}

	if(sf_quest_list == '0'){
		alert("Select question please!");
		return false;
	}

	console.log("test");

	questionsStack[liID].rules.push({question: sf_quest_list, answer: sf_field_list, option: new_option, priority: new_priority});

	var field_list = $("#sf_field_list option:selected").text();
	var quest_list = $("#sf_quest_list option:selected").text();
	var option_list = $("#sf_option_list option:selected").text();

	var htmlOption = (new_option) ? '<td class="rule_option_'+liID+'_' + (new_option - 1) + '">' + option_list + '</td>' : '';

	var tr = '<tr><td>&nbsp;</td>' + htmlOption + '<td class="rule_answ_'+liID+'_' + (sf_field_list - 1) + '">' + field_list + '</td><td class="rule_' + sf_quest_list + '">' + quest_list + '</td><td>' + new_priority + '</td><td><a class="remove hide-remove" href="#" onclick="removeRule(this);"><i class="fa fa-times"></i></a></td></tr>';
	$(qfld_tbl_rule).append(tr);

	$("#sf_option_list").val('');
	$("#sf_field_list").val('');
	$("#sf_quest_list").val('');
	$("#new_priority").val('');
}

var getRulesQuestion = function()
{
	var isOption = false;
	var liID = $(currQuestion).attr("id");
	var trs = $("#qfld_tbl_rule tbody tr").not("tr:first");
	trs.html('');
	var tbody = $("#qfld_tbl_rule tbody");

	var rules = questionsStack[liID].rules;
	var tr = '<tr><th align="center" width="2%">#</th><th width="25%" class="title rule_option" style="display:none;">Option</th><th width="25%" class="title">Answer</th><th width="25%" class="title">Question</th><th width="10%" class="title">priority </th><th width="auto"></th></tr>';

	if(currQuestion && rules.length){
		for (var n = 0; n < rules.length; n++) {

			if(!questionsStack[rules[n].question]){
				continue;
			}

			var title = questionsStack[rules[n].question].sf_qtitle;
			title = (title.length >= 30) ? title.substr(0, 30)+"..." : title;
			var priority = rules[n].priority;
			var qtype = questionsStack[liID].sf_qtype;
			var ans = rules[n].answer - 1;
			var opt = (rules[n].option) ? rules[n].option - 1 : '-1';

			if(questionsStack[liID].answers[ans]){
				switch(qtype){
					case 'pick-one'	:
					case 'pick-many':
						ans_data = questionsStack[liID].answers[ans].title;
					break;
					case 'ranking-dropdown':
					case 'ranking-dragdrop':
						isOption = true;
						var ans_data = questionsStack[liID].answers[ans].right;
						var opt_data = questionsStack[liID].answers[opt].left;
					break;
					case 'ranking':
						isOption = true;
						var ans_data = questionsStack[liID].answers.ranks[ans];
						var opt_data = questionsStack[liID].answers.options[opt];
					break;
					case 'likert-scale':
						isOption = true;
						var ans_data = questionsStack[liID].answers.scales[ans];
						var opt_data = questionsStack[liID].answers.options[opt];
					break;
				}

				var optionHtml = (isOption) ? '<td class="rule_option_' + rules[n].question + '_'+ opt +'">' + opt_data + '</td>' : '';

				var tr = tr + '<tr><td>&nbsp;</td>' + optionHtml + '<td class="rule_answ_' + rules[n].question + '_'+ ans +'">' + ans_data + '</td><td class="rule_' + rules[n].question + '">' + title + '</td><td>' + priority + '</td><td><a class="remove hide-remove" href="#" onclick="removeRule(this);"><i class="fa fa-times"></i></a></td></tr>';
			}
		}

		tbody.html(tr);
		if(isOption) $(".rule_option").show();
	}
}

var removeRule = function(elem)
{
	var liID = $(currQuestion).attr("id");
	var tr = $(elem).parent().parent();

	var index = $(tr).index();
	questionsStack[liID].rules.splice(index - 1, 1);

	$(tr).fadeOut(300, function(){
		$(tr).remove();
	});
}

var sfSaveSurvey = function(wasClick)
{
	$(".viewport").css("opacity", 0.4);
	var surveyForm = document.getElementById("surveyForm");

	var formData = new FormData(surveyForm);
	var xhr = new XMLHttpRequest();
	xhr.open("POST", "index.php?option=com_surveyforce&task=survey.saveSurvey&tmpl=component");

	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4) {
			if(xhr.status == 200) {
				js = xhr.responseText;
				
				if(js == 'no login'){
					parent.location.reload();
					window.close();
					return false;
				}

				var surv_id = ($("#survey_id").val()) ? $("#survey_id").val() : 0;
				$.ajax({
					url: "index.php?option=com_surveyforce&task=survey.saveQuestions&tmpl=component&surv_id=" + surv_id,
					type: "POST",
					data: { json: JSON.stringify(questionsStack) },
					success: function(json)
					{
						eval(js);
						sfParseJSON(json);
						$(".viewport").css("opacity", 1);
					}
				});
			}
		}
	};
	
	xhr.send(formData);

	if(!firstSave || wasClick){
		var autosavePeriod = parseInt($("#autosave").val());
		var timeOut = 60 * 1000 * autosavePeriod;
		if(timeOut < 60 * 1000) return;

		if(typeof timer != 'undefined') clearInterval(timer);
		
		timer = setInterval("sfSaveSurvey(false)", timeOut);
		firstSave = true;
	}

	return false;
}

var sfParseJSON = function(json)
{	
	if(json){
		if(!(json instanceof Object)) json = JSON.parse(json);
		for(var systemid in json['questions']){
			questionsStack[systemid].id = parseInt(json['questions'][systemid]);
		}

		for(var systemid in json['answers']){
			var qtype = questionsStack[systemid].sf_qtype;

			switch(qtype){
				case 'pick-one':
				case 'pick-many':
					if(json['answers'][systemid].length){
						for(var len = json['answers'][systemid].length, n = 0; n < len; n++){
							questionsStack[systemid].answers[n].id = parseInt(json['answers'][systemid][n]);
						}
					}
				break;
				case 'ranking-dropdown':
				case 'ranking-dragdrop':
					if(json['answers'][systemid]['leftid'].length){
						for(var len = json['answers'][systemid].length, n = 0; n < len; n++){
							questionsStack[systemid].answers[n].leftid = parseInt(json['answers'][systemid]['leftid'][n]);
							questionsStack[systemid].answers[n].rightid = parseInt(json['answers'][systemid]['rightid'][n]);
						}
					}
				break;
				case 'ranking':
				case 'likert-scale':

					if(json['answers'][systemid]['oid'].length){
						questionsStack[systemid].answers['oid'] = [];
						for(var len = json['answers'][systemid]['oid'].length, n = 0; n < len; n++){
							questionsStack[systemid].answers['oid'].push(parseInt(json['answers'][systemid]['oid'][n]));
						}
					}

					var kid = (qtype == 'ranking') ? 'rid' : 'sid';
					if(json['answers'][systemid][kid].length){
						questionsStack[systemid].answers[kid] = [];
						for(var len = json['answers'][systemid][kid].length, n = 0; n < len; n++){
							questionsStack[systemid].answers[kid].push(parseInt(json['answers'][systemid][kid][n]));
						}
					}

				break;
			}
		}
	}
}