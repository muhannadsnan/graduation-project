-- phpMyAdmin SQL Dump
-- version 2.10.3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 04, 2015 at 08:19 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `hadara`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `ads`
-- 

CREATE TABLE `ads` (
  `ads_id` varchar(32) default NULL,
  `ads_title_ar` varchar(100) default NULL,
  `ads_title_en` varchar(100) default NULL,
  `ads_pic` varchar(10) default NULL,
  `ads_link` text,
  `ads_start` datetime default NULL,
  `ads_end` datetime default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `ads`
-- 

INSERT INTO `ads` VALUES ('1', 'إصدار جديد من برنامج المحاسبة يعمل على متصفحات الويب وأين ماكنت', 'A new version of the accounting program running on web browsers and where Makint', 'jpg', 'www.google.com', '2014-01-01 17:55:24', '2015-12-31 17:55:30', '2014-01-01 17:55:38');
INSERT INTO `ads` VALUES ('2', 'تحديث جديد لبرنامج ذاتية الموظفين الاصدار 1040', 'Reload new self-release program staff in 1040', 'jpg', 'www.google.com', '2014-01-01 17:56:22', '2015-12-31 17:56:27', '2014-01-01 17:56:36');
INSERT INTO `ads` VALUES ('3', 'تحديث جديد لبرنامج الرواتب والاجور الاصدار 1145', 'A new software update release of salaries and wages in 1145', 'jpg', 'www.google.com', '2014-01-01 17:57:15', '2015-12-31 17:57:21', '2014-01-01 17:57:29');
INSERT INTO `ads` VALUES ('4', 'تحديث جديد لبرنامج أوامر الدفع والقبض الاصدار 2600', 'A new software update payment orders and arrest Released in 2600', 'jpg', 'www.google.com', '2014-01-01 17:58:08', '2015-12-31 17:58:13', '2014-01-01 17:58:21');
INSERT INTO `ads` VALUES ('5', 'تحديث جديد لبرنامج المحاسبة والمستودعات الاصدار 1800', 'A new software update accounting and warehouses Released in 1800', 'jpg', 'www.google.com', '2014-01-01 17:58:52', '2015-12-31 17:58:58', '2014-01-01 17:59:04');
INSERT INTO `ads` VALUES ('6', 'title_ar', 'title_en', 'png', 'http://www.facebook.com', '2014-10-19 22:00:05', '2015-10-19 22:00:05', '2014-10-19 22:00:05');
INSERT INTO `ads` VALUES ('007', 'title_ar', 'title_en', 'png', 'www.facebook.com', '2014-10-19 22:00:05', '2014-12-22 22:00:05', '2014-10-19 22:00:05');
INSERT INTO `ads` VALUES ('008', 'title_ar', 'title_en', 'png', 'www.facebook.com', '2014-05-19 22:00:05', '2014-11-22 22:00:05', '2014-10-19 22:00:05');
INSERT INTO `ads` VALUES ('009', 'title_ar', 'title_en', 'png', 'www.facebook.com', '2014-06-19 22:00:05', '2015-01-22 22:00:05', '2014-10-19 22:00:05');
INSERT INTO `ads` VALUES ('010', 'title_ar', 'title_en', 'png', 'www.facebook.com', '2014-07-19 22:00:05', '2014-12-03 22:00:05', '2014-10-19 22:00:05');

-- --------------------------------------------------------

-- 
-- Table structure for table `album`
-- 

CREATE TABLE `album` (
  `album_id` varchar(32) default NULL,
  `album_title_ar` varchar(100) default NULL,
  `album_title_en` varchar(100) default NULL,
  `NVNom` int(11) default '0',
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `album`
-- 

INSERT INTO `album` VALUES ('2', 'صور برامج القطاع الخاص', 'Photos of the private sector programs', 1, '2014-01-01 18:00:53');
INSERT INTO `album` VALUES ('3', 'صور برامج خاصة', 'Photo special programs', 4, '2014-01-01 18:01:17');
INSERT INTO `album` VALUES ('54a80da875491', 'احمد الناشف', '', 0, '2015-01-03 17:41:28');

-- --------------------------------------------------------

-- 
-- Table structure for table `buy_contract`
-- 

CREATE TABLE `buy_contract` (
  `bcont_dist` varchar(32) default NULL,
  `bcont_cust` varchar(32) default NULL,
  `NSerial` int(11) default NULL,
  `bcont_prod` varchar(32) default NULL,
  `bcont_discount` float default NULL,
  `bcont_payment` float default NULL,
  `bcont_seen` tinyint(1) default NULL,
  `bcont_license` varchar(40) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `buy_contract`
-- 

INSERT INTO `buy_contract` VALUES ('distID_ew3453463ef', '549d99c87e38b', 54, '003', 0, 6, 0, 'No License', '2015-01-02 13:22:23');
INSERT INTO `buy_contract` VALUES ('muhannadid', '549d99c87e38b', 56, '005', 0, 0, 0, 'No License', '2015-01-02 13:33:30');
INSERT INTO `buy_contract` VALUES ('muhannadid', '549d99e3e0870', 57, 'this is id', 9, 100, 0, 'No License', '2015-01-02 13:40:56');
INSERT INTO `buy_contract` VALUES ('muhannadid', '549d99c87e38b', 58, '003', 0, 0, 0, 'No License', '2015-01-02 13:41:57');
INSERT INTO `buy_contract` VALUES ('distID_ew3453463ef', '549d99e3e0870', 54, '002', 55, 99, 0, 'No License', '2015-01-03 17:36:03');
INSERT INTO `buy_contract` VALUES ('distID_ew3453463ef', '549d99c87e38b', 54, '54a9610ece334', 0, 0, 0, 'No License', '2015-01-04 18:39:54');
INSERT INTO `buy_contract` VALUES (NULL, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `buy_contract` VALUES (NULL, NULL, 3, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `buy_contract` VALUES (NULL, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `buy_contract` VALUES (NULL, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `buy_contract` VALUES (NULL, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `buy_contract` VALUES ('distID_ew3453463ef', '549d99c87e38b', 59, '54a9610ece334', 0, 0, 0, 'No License', '2015-01-04 19:02:52');
INSERT INTO `buy_contract` VALUES ('distID_ew3453463ef', '549d99c87e38b', 60, '54a9610ece334', 0, 0, 0, 'No License', '2015-01-04 19:03:02');

-- --------------------------------------------------------

-- 
-- Table structure for table `category`
-- 

CREATE TABLE `category` (
  `cat_id` varchar(32) default NULL,
  `cat_title_ar` varchar(50) default NULL,
  `cat_title_en` varchar(50) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `category`
-- 

INSERT INTO `category` VALUES ('001', 'برامج القطاع العام', 'Public sector programs', '2014-01-01 18:46:05');
INSERT INTO `category` VALUES ('002', 'برامج القطاع الخاص', 'Private sector programs', '2014-01-01 18:46:36');
INSERT INTO `category` VALUES ('003', 'برامج خاصة', 'Special programs', '2014-01-01 18:47:00');
INSERT INTO `category` VALUES ('004', 'برامج مساعدة', 'Supplying Applications', '2015-01-03 20:59:48');

-- --------------------------------------------------------

-- 
-- Table structure for table `contactus`
-- 

CREATE TABLE `contactus` (
  `cu_id` varchar(32) default NULL,
  `cu_name` varchar(50) default NULL,
  `cu_mobile` varchar(20) default NULL,
  `cu_job` text,
  `cu_tel` varchar(20) default NULL,
  `cu_company` text,
  `cu_email` varchar(50) default NULL,
  `cu_pic` varchar(10) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `contactus`
-- 

INSERT INTO `contactus` VALUES ('ar', 'شركة إم أي للبرمجيات', '0999887769', 'تقوم الشركة بتطوير أنظمة مالية وإدارية باتباع أحدث وأرقى المعايير التقنية والمحاسبية العالمية لتقدم لعملائها حلول برمجية متطورة بتقنيتها ومضمونها مع ملائمتها لواقع السوق المحلي في المنطقة العربية ومنطقة الشرق الأوسط وتلبيتها لمتطلباته و الحرص على تقديم أفضل خدمة لعملائها وخصوصا في مرحلة ما بعد البيع لنكون بحق شركاء النجاح مع عملائنا قولا وفعلا .\r\n', '0113333330', 'قامت شركتنا بتطوير مجموعة أنظمة لأتمتة عمل الشركات العامة والخاصة والتي تشمل:الأنظمة المالية،أنظمة الموارد البشرية (للقطاعين العام والخاص)،أنظمة خاصة.\r\n', 'info@ma.compamy.com', 'jpg');
INSERT INTO `contactus` VALUES ('en', 'M&A Company', '0999887769', 'The company is developing financial and administrative systems following the latest and finest technical and accounting to international standards to provide its clients with software solutions developed Ptguenitha and content with the suitability of the reality of the local market in the Arab region and the Middle East and meet the requirements and care to provide the best service to its customers, especially in the post-sale to be truly successful partners with our customers in word and deed.\r\n', '0113333330', 'Our company has developed a system to automate the work of public and private companies, which include: financial systems, HR systems (public and private sectors), private systems.\r\n', 'info@ma.compamy.com', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `faq`
-- 

CREATE TABLE `faq` (
  `faq_id` varchar(32) default NULL,
  `faq_title_ar` varchar(150) default NULL,
  `faq_title_en` varchar(150) default NULL,
  `faq_desc_ar` text,
  `faq_desc_en` text,
  `NVNom` int(11) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `faq`
-- 

INSERT INTO `faq` VALUES ('001', 'كيف أنشئ قاعدة بيانات جديدة في البرنامج', 'How do I create a new database in the program', 'لإنشاء قاعدة بيانات جديدة في أي برنامج نختار من القائمة "ملف" الخيار "إنشاء قاعدة بيانات جديدة" نحدد اسم قاعدة البيانات ونوع قاعدة البيانات ومسار النسخ ونضغط أخيرا على "إنشاء" \r\n', 'To create a new database in any program we choose the "File" option "Create a new database list" define the database and the type of database and backup path name finally put pressure on the "Create"\r\n', NULL, '0000-00-00 00:00:00');
INSERT INTO `faq` VALUES ('002', 'ما هي خطوات تدوير قاعدة بيانات محاسبية', 'What are the steps rotate the accounting database', 'لتدوير قاعدة بيانات محاسبية نقوم أولاً بإنشاء قاعدة بيانات جديدة فارغة ثم نختار من القائمة "أدوات" الخيار "تدوير قاعدة بيانات" نحدد قاعدة البيانات الأصلية التي نريد تدويرها ثم نحدد قاعدة البيانات الجديدة الفارغة وبعد الضغط على "التالي" نحدد ما هي المعلومات التي نريد تدويرها وأخيرا نضغط "إنهاء"\r\n', 'To rotate the accounting data, we first create a new database is empty and then choose from the "Tools" menu "recycling database" option define the original data that we want to recycle and then define new data empty base base and after pressing the "Next" Base define what information we want to recycle Finally we press "Finish"\r\n', NULL, '0000-00-00 00:00:00');
INSERT INTO `faq` VALUES ('003', 'كيف استخدم الآلة الحاسبة في برنامج المحاسبة', 'How do I use a calculator in the accounting program', 'يتميز برنامج المحاسبة والمستودعات باحتوائه على آلة حاسبة ضمن حقوله حيث ان مستخدم البرنامج يستطيع استخدام الآلة الحاسبة ضمن أي حقل يقف عليه ثم "Enter" لإظهار ناتج العمليات\r\n', 'Accounting and warehouses Program Featuring a calculator within the fields where the user program can use the calculator in any field it stands then "Enter" to show the result of operations\r\n', NULL, '0000-00-00 00:00:00');
INSERT INTO `faq` VALUES ('004', 'ما هي خطوات ترفيع الموظفين في برنامج الرواتب القطاع العام', 'What are the steps to upgrade staff salaries in the public sector program', 'لترفيع رواتب الموظفين في برنامج الرواتب والأجور نختار من القائمة "ملف" الخيار "جدول ترفيعات الموظفين" نحدد العام والعنوان وعدد أيام الدوام الكلي الافتراضي ثم "إضافة" بعدها نضيف على هذا الجدول أسماء الموظفين ونحدد عدد أيام دوام كل موظف مع نسبة التقييم وأخيرا نضغط "حساب"\r\n', 'To upgrade the salaries of employees in wages and salaries program choose from the "File" option "list Tervaat Employees table" define the public address and the number of working days total default and then "Add" Then we add to this table employee names and determine the number of days time each employee with the proportion of evaluation and finally pressing "account "\r\n', NULL, '0000-00-00 00:00:00');
INSERT INTO `faq` VALUES ('005', 'كيف أطبع بطاقة ذاتية لموظف للقطاع العام', 'How do I print a self card for employees of the public sector', 'لطباعة بطاقة ذاتية لموظف في برنامج ذاتية الموظفين نختار من بطاقة الذاتية ومن القائمة "أدوات" فيها الخيار "طباعة" ثم نختار التصميم المطلوب وحجم الورق وأخيرا نضغط على "طباعة"\r\n', 'To print a self-card to an employee in the staff Resume program choose from self-card option is the "Tools" menu in the "Print" and then choose the desired design and size of the paper and finally pressing the "Print"\r\n', NULL, '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- 
-- Table structure for table `groups`
-- 

CREATE TABLE `groups` (
  `group_id` varchar(32) default NULL,
  `group_name` varchar(50) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `groups`
-- 

INSERT INTO `groups` VALUES ('admins0000', 'ADMINISTRATORS');
INSERT INTO `groups` VALUES ('myGR', 'TEST GROUP');
INSERT INTO `groups` VALUES ('muhannadGRid', 'muhannadGR');
INSERT INTO `groups` VALUES ('أحمد الناشف ID', 'أحمد الناشف');

-- --------------------------------------------------------

-- 
-- Table structure for table `job`
-- 

CREATE TABLE `job` (
  `job_id` varchar(32) default NULL,
  `job_title_ar` varchar(100) default NULL,
  `job_title_en` varchar(100) default NULL,
  `job_desc_ar` text,
  `job_desc_en` text,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `job`
-- 

INSERT INTO `job` VALUES ('001', 'مطلوب محاسب لشركة وتار للصناعات الالكترونية', 'Accountant required for Watar company  Electronic Industries', 'مميزات العمل: دراسة جامعية كلية الاقتصاد أو معهد تجاري مع خبرة ممارسة عملية طويلة، خبرة في نفس برنامج الشركة للمحاسبة والمستودعات، إنهاء خدمة العلم أو تأجيل طويل، التفرغ التام للعمل وتحمل ضغوط العمل وحب العمل في فريق مع عدم التدخين  \r\n', 'Features Work: university study Faculty of Economics Institute or commercial experience with the practice of a long process, experience in the same company for accounting and warehouse program, termination of science or a long delay, full-time work and endure the pressures of work and love to work in a team with a non-smoking service\r\n', '2014-12-01 18:56:20');
INSERT INTO `job` VALUES ('002', 'مطلوب محاسب لشركة كراش ', 'Accountant required for Crash company ', 'مميزات العمل: دراسة جامعية كلية الاقتصاد أو معهد تجاري مع خبرة ممارسة عملية طويلة، خبرة في نفس برنامج الشركة للمحاسبة والمستودعات، إنهاء خدمة العلم أو تأجيل طويل، التفرغ التام للعمل وتحمل ضغوط العمل وحب العمل في فريق مع عدم التدخين  \r\n', 'Features Work: university study Faculty of Economics Institute or commercial experience with the practice of a long process, experience in the same company for accounting and warehouse program, termination of science or a long delay, full-time work and endure the pressures of work and love to work in a team with a non-smoking service\r\n', '2014-12-02 18:57:00');
INSERT INTO `job` VALUES ('003', 'مطلوب محاسب لشركة كتاكيت للصناعات الغذائية', 'Accountant required for the Company for Food Industries chicks', 'مميزات العمل: دراسة جامعية كلية الاقتصاد أو معهد تجاري مع خبرة ممارسة عملية طويلة، خبرة في نفس برنامج الشركة للمحاسبة والمستودعات، إنهاء خدمة العلم أو تأجيل طويل، التفرغ التام للعمل وتحمل ضغوط العمل وحب العمل في فريق مع عدم التدخين  \r\n', 'Features Work: university study Faculty of Economics Institute or commercial experience with the practice of a long process, experience in the same company for accounting and warehouse program, termination of science or a long delay, full-time work and endure the pressures of work and love to work in a team with a non-smoking service\r\n', '2014-12-03 18:57:42');
INSERT INTO `job` VALUES ('004', 'مطلوب محاسب لمستودع الكردي للأغذية', 'Accountant for warehouse Kurdish Food required', 'مميزات العمل: دراسة جامعية كلية الاقتصاد أو معهد تجاري مع خبرة ممارسة عملية طويلة، خبرة في نفس برنامج الشركة للمحاسبة والمستودعات، إنهاء خدمة العلم أو تأجيل طويل، التفرغ التام للعمل وتحمل ضغوط العمل وحب العمل في فريق مع عدم التدخين  \r\n', 'Features Work: university study Faculty of Economics Institute or commercial experience with the practice of a long process, experience in the same company for accounting and warehouse program, termination of science or a long delay, full-time work and endure the pressures of work and love to work in a team with a non-smoking service\r\n', '2014-12-04 18:58:24');
INSERT INTO `job` VALUES ('005', 'مطلوب محاسب لشركة أدوية عالمية', 'Accountant required for a global pharmaceutical company', 'مميزات العمل: دراسة جامعية كلية الاقتصاد أو معهد تجاري مع خبرة ممارسة عملية طويلة، خبرة في نفس برنامج الشركة للمحاسبة والمستودعات، إنهاء خدمة العلم أو تأجيل طويل، التفرغ التام للعمل وتحمل ضغوط العمل وحب العمل في فريق مع عدم التدخين  \r\n', 'Features Work: university study Faculty of Economics Institute or commercial experience with the practice of a long process, experience in the same company for accounting and warehouse program, termination of science or a long delay, full-time work and endure the pressures of work and love to work in a team with a non-smoking service\r\n', '2014-12-09 18:59:01');

-- --------------------------------------------------------

-- 
-- Table structure for table `mail_list`
-- 

CREATE TABLE `mail_list` (
  `email` varchar(50) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `mail_list`
-- 

INSERT INTO `mail_list` VALUES ('abc');
INSERT INTO `mail_list` VALUES ('muhannad@msn.com');
INSERT INTO `mail_list` VALUES ('ahmed');
INSERT INTO `mail_list` VALUES ('mmmmm');
INSERT INTO `mail_list` VALUES ('bbbb');
INSERT INTO `mail_list` VALUES ('5');
INSERT INTO `mail_list` VALUES ('a');

-- --------------------------------------------------------

-- 
-- Table structure for table `maint_contract`
-- 

CREATE TABLE `maint_contract` (
  `NSerial` varchar(32) default NULL,
  `mcont_dist` varchar(32) default NULL,
  `mcont_cust` varchar(32) default NULL,
  `bcont_serial` varchar(32) default NULL,
  `mcont_desc` text,
  `mcont_seen` tinyint(1) default NULL,
  `mcont_status` varchar(20) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `maint_contract`
-- 

INSERT INTO `maint_contract` VALUES ('1', 'distID_ew3453463ef', '549d99c87e38b', '57', '', 0, 'pending', '2015-01-02 15:25:59');
INSERT INTO `maint_contract` VALUES ('54a69c124f96d', 'distID_ew3453463ef', '549d99c87e38b', '54', '', 0, 'pending', '2015-01-02 15:24:34');
INSERT INTO `maint_contract` VALUES ('54a69c1447139', 'distID_ew3453463ef', '549d99c87e38b', '54', '', 0, 'pending', '2015-01-02 15:24:36');
INSERT INTO `maint_contract` VALUES ('54a69c72790cf', 'distID_ew3453463ef', '549d99c87e38b', '54', '', 0, 'pending', '2015-01-02 15:26:10');
INSERT INTO `maint_contract` VALUES ('54a6a08427437', 'distID_ew3453463ef', '549d99c87e38b', '58', '', 0, 'pending', '2015-01-02 15:43:32');
INSERT INTO `maint_contract` VALUES ('54a82d542ff85', 'distID_ew3453463ef', '549d99c87e38b', '54', '', 0, 'pending', '2015-01-03 19:56:36');
INSERT INTO `maint_contract` VALUES ('54a97c4f0d40d', 'distID_ew3453463ef', '549d99c87e38b', '60', '', 0, 'pending', '2015-01-04 19:45:51');
INSERT INTO `maint_contract` VALUES ('54a97c6c1ac5e', 'muhannadid', '549d99e3e0870', '60', '', 0, 'pending', '2015-01-04 19:46:20');
INSERT INTO `maint_contract` VALUES ('55', 'distID_ew3453463ef', '549d99c87e38b', '2', '', 0, 'pending', '2015-01-04 20:17:06');
INSERT INTO `maint_contract` VALUES ('56', 'distID_ew3453463ef', '549d99c87e38b', '2', '', 0, 'pending', '2015-01-04 20:17:23');

-- --------------------------------------------------------

-- 
-- Table structure for table `news`
-- 

CREATE TABLE `news` (
  `news_id` varchar(32) default NULL,
  `news_title_ar` varchar(60) default NULL,
  `news_title_en` varchar(60) default NULL,
  `news_desc_ar` varchar(200) default NULL,
  `news_desc_en` varchar(200) default NULL,
  `news_text_ar` text,
  `news_text_en` text,
  `news_pic` varchar(10) default NULL,
  `NVNom` int(11) default '0',
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `news`
-- 

INSERT INTO `news` VALUES ('001', 'التكيف مع احتياجات السوق', 'Adapt to market needs', 'برامج شركتنا هي الأكثر شعبية في سوق العمل', 'Our programs are most popular in the labor market', 'برنامج المحاسبة والمستودعات يصادف الذكرى العاشرة من مقدمي برامج الأكثر شعبية في سوريا خلال معرض شام للتكنولوجيا 2014 حيث أبرز جدارته بواجهاته سهلة الاستخدام وبإدائه العالي وقدرته الفائقة على التكيف مع مجالات العمل كافة بالاضافة إلى سعره المناسب والمدروس ليناسب جميع شرائح المجتمع.\r\n', 'Accounting and warehouses program marks the tenth anniversary of the sponsors of the most popular programs in Syria during the Cham of Technology in 2014 where he highlighted his worth is easy to use and Higher his performance and his superior ability to adapt to all areas of work Busbandath addition to the appropriate and thoughtful price to suit all segments of society.\r\n', 'png', 4, '2014-01-01 19:04:42');
INSERT INTO `news` VALUES ('002', 'تحسينات كبيرة لمستخدمي ماك', 'Significant improvements for Mac users', 'برامج شركتنا متاحة الآن لمستخدمي نظام التشغيل ماك', 'Our programs are now available for users of Mac OS', 'لقد تم تطوير برنامج المحاسبة والمستودعات ليعمل على نظام تشغيل ماك وذلك يعتبر خطوة جريئة لفتح سوق جديد للشركة مع دول أوربية حيث أن البرامج يدعم اللغة الانكليزية والفرنسية بالاضافة إلى اللغة العربية، وايضا تم إضافة ميزات جديدة خاصة بنظام التشغيل هذا ليكون البرنامج متوافق مع هذا النوع من الأنظمة.\r\n', 'I''ve been accounting program and warehouse development to run on Mac OS and it is a bold step to open a new market for the company with the European countries where the software supports English and French in addition to the Arabic language, and also added new special features of this operating system software to be compatible with this type of systems.\r\n', 'png', 0, '2014-02-01 19:05:51');
INSERT INTO `news` VALUES ('003', 'تحسينات كبيرة لمستخدمي الأندرويد', 'Significant improvements for users of Android', 'برامج شركتنا متاحة الآن لمستخدمي نظام التشغيل أندرويد على الأجهزة الذكية أو التابليت', 'Our programs are now available for users of Android running on smart devices or Altablat system', 'لقد تم تطوير برنامج المحاسبة والمستودعات ليعمل على نظام تشغيل أندرويد وذلك من اجل تسهيل العمل والتواصل بين البرنامج والمستخدمين، من خلال إضافة الأدوات الأكثر استخدام لهذا الاستخدام وغالبا اضافة التقارير التي تبين للمستخدمين كيفية سير العمل لمتابعته.\r\n', 'I''ve been accounting program and warehouse development to run on the Android operating system in order to facilitate the work and communication between the software and users, through the addition of more use for this use tools and often adding reports that show users how to workflow to follow.\r\n', 'png', 0, '2014-03-01 19:06:50');
INSERT INTO `news` VALUES ('004', 'تحسينات كبيرة لمستخدمي لينكس', 'Significant improvements to Linux users', 'برامج شركتنا متاحة الآن لمستخدمي نظام التشغيل لينكس', 'Our programs are now available to users of the Linux operating system', 'لقد تم تطوير برنامج المحاسبة والمستودعات ليعمل على نظام تشغيل لينكس وذلك يعتبر خطوة جريئة لفتح سوق جديد للشركة مع محبي هذه المنصة من دول عربية وأجنبية حيث أن البرامج يدعم اللغة الانكليزية والفرنسية بالاضافة إلى اللغة العربية، وايضا تم إضافة ميزات جديدة خاصة بنظام التشغيل هذا ليكون البرنامج متوافق مع هذا النوع من الأنظمة.\r\n', 'I''ve been accounting program and warehouse development to run on the Linux operating system and it is a bold step to open a new market for the company with the lovers of this platform from Arab and foreign countries where the software supports English and French in addition to the Arabic language, and also added new special features of this operating system to be the program is compatible with this type of system.\r\n', 'png', 5, '2014-04-01 19:07:49');
INSERT INTO `news` VALUES ('005', 'الاصدارات المحدثة من البرامج', 'Updated versions of the software', 'برنامج المحاسبة 1.2.2600 الاصدار النهائي', '  Accounting 1.2.2600 Program Final Release', 'تم اصدار تحديث 2600 لبرنامج المحاسبة والمستودعات وهو الاصدار النهائي من البرنامج يحوي هذا التحديث على ميزات جديدة وهي: إضافة ميزة الجرد الفرعي حسب مواصفات المادة، إضافة إمكانية عرض القيود حسب فرع أو قسم أو مركز كلفة، إضافة ميزة جديدة تخص الصلاحيات في البرنامج.\r\n', 'An update of the 2600 program accounting, warehouse, a final version of the program contains this update on new features, namely: the addition of sub inventory feature as material specifications, as well as the ability to view the restrictions as a branch or department or cost center, as well as a new feature specific to the powers in the program.\r\n', 'png', 7, '2014-05-01 19:12:20');
INSERT INTO `news` VALUES ('006', 'الاصدارات المحدثة من البرامج', 'Updated versions of the software', 'برنامج الرواتب والأجور 1.1.1150 الاصدار النهائي', 'Salaries wages program 1.1.1150 Final Release', 'تم اصدار تحديث 1150 لبرنامج الرواتب والأجور وهو الاصدار النهائي من البرنامج يحوي هذا التحديث على ميزات جديدة وهي: إضافة إمكانية إيقاف تعويض بتاريخ معين ثم إعادة تفعيله من جديد، إضافة خيارات جديدة لطباعة الفيش بقياسات جديدة، إضافة ميزة جديدة تخص الصلاحيات في البرنامج.\r\n', 'An update 1150 program salaries and wages, a final version of the program contains this update on new features, namely: to add the possibility of stopping compensate a certain date and then re-activated again, adding new options to print Alves new measurements, as well as a new feature specific to the powers in the program.\r\n', 'png', 33, '2014-06-01 19:20:01');

-- --------------------------------------------------------

-- 
-- Table structure for table `picture`
-- 

CREATE TABLE `picture` (
  `pic_id` varchar(32) default NULL,
  `pic_album` varchar(32) default NULL,
  `pic_title_ar` varchar(100) default NULL,
  `pic_title_en` varchar(100) default NULL,
  `pic_video` varchar(32) default NULL,
  `pic_ext` varchar(10) NOT NULL,
  `NVNom` int(11) default '0',
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `picture`
-- 

INSERT INTO `picture` VALUES ('001', '2', 'واجهة برنامج ذاتية الموظفين', 'Form for personal employee program', NULL, '', 0, '2014-01-01 19:22:10');
INSERT INTO `picture` VALUES ('002', '2', 'واجهة برنامج رواتب وأجور', 'Form for Wages and salaries program', '', 'jpg', 0, '2014-01-01 19:22:47');
INSERT INTO `picture` VALUES ('003', '2', 'واجهة برنامج أوامر الدفع والقبض', 'Form for Payment orders and arrest program', '', 'jpg', 0, '2014-01-01 19:23:20');
INSERT INTO `picture` VALUES ('004', '2', 'واجهة برنامج المحاسبة والمستودعات', 'Form for Accounting and warehouses program', '', 'jpg', 0, '2014-01-01 19:24:00');
INSERT INTO `picture` VALUES ('005', '002', 'واجهة برنامج الصيادلة', 'Form for Pharmacists program', NULL, '', 0, '2014-01-01 19:24:37');
INSERT INTO `picture` VALUES ('006', '003', 'واجهة برنامج شركة كهرباء دمشق', 'Form for Damascus Electricity Company Program', NULL, '', 0, '2014-01-01 19:25:06');
INSERT INTO `picture` VALUES ('54a80f6768450', '54a80da875491', 'احمد12', '', '', 'jpg', 0, '2015-01-03 17:49:03');
INSERT INTO `picture` VALUES ('54a80e5e81bfc', '54a80da875491', 'احمد1', '', '', 'jpg', 0, '2015-01-03 17:44:52');
INSERT INTO `picture` VALUES ('54a5fa49d5099', '2', 'aaaa', 'ddddds', 'aaaa', 'jpg', 0, '2015-01-02 03:52:44');
INSERT INTO `picture` VALUES ('54a84fadd1750', '54a80da875491', '3', '', '', 'jpg', 0, '2015-01-03 22:23:22');
INSERT INTO `picture` VALUES ('54a938af8d7f0', '54a9370fefe41', '', '', '', 'jpg', 0, '2015-01-04 14:57:22');
INSERT INTO `picture` VALUES ('54a93651010d1', '3', 'ab', '', '', 'jpg', 0, '2015-01-04 14:47:22');

-- --------------------------------------------------------

-- 
-- Table structure for table `product`
-- 

CREATE TABLE `product` (
  `prod_id` varchar(32) default NULL,
  `prod_title_ar` varchar(100) default NULL,
  `prod_title_en` varchar(100) default NULL,
  `prod_cat` varchar(32) default NULL,
  `prod_desc_ar` varchar(200) default NULL,
  `prod_desc_en` varchar(200) default NULL,
  `prod_text_ar` text,
  `prod_text_en` text,
  `prod_pic` varchar(10) default NULL,
  `NVNom` int(20) default '0',
  `NDate` datetime default NULL,
  `prod_only_dw` tinyint(1) default NULL,
  `prod_exe` varchar(60) default NULL,
  `prod_price` float default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `product`
-- 

INSERT INTO `product` VALUES ('001', 'برنامج ذاتية الموظفين', 'personal employee program', '001', 'برنامج لاتمتة عمل قسم الشؤون الادارية ', 'Program to automate the work of the Department of Administrative Affairs', 'مهام برنامج الذاتية حفظ كل المعلومات الذاتية للموظفين مثل المعلومات الشخصية، الوضع الوظيفي، الاجازات بلا اجر، العقوبات، الحالة الصحية وغيرها. ويتضمن البرنامج عدة تقارير للاستعلام عن هذه البيانات مثل تقرير الذاتية، تقرير عدد العاملين حسب عمر معين، تقرير الوضع الوظيفي للعاملين وغيره. ويتميز البرنامج بكونه مخصص للاستخدام حسب صلاحيات يحددها مدير المنظومة فيها مجالات واسعة.\r\n', 'Self-keeping functions of each program self information to employees, such as personal information, employment status, unpaid vacations, sanctions, health status, and others. The program includes several reports to inquire about this data, such as self-report, the report of the number of employees by a certain age, employment status of workers and the other report. The program is characterized by being dedicated for use by the powers defined by the system manager in broad areas.\r\n', 'png', 65, '2014-01-01 19:26:56', 0, 'exe', 0);
INSERT INTO `product` VALUES ('002', 'برنامج رواتب وأجور', 'Wages and salaries program', '001', 'برنامج لحساب الرواتب والأجور مع التوطين', 'Program to calculate salaries and wages with resettlement', 'مهام برنامج الرواتب حفظ المعلومات المتعلقة بحساب الرواتب مثل راتب الموظف،الفئة،التعويضات، الحسميات، الأقساط، معلومات التوطين وغيرها. يتضمن البرنامج عدة تقارير منها تقرير الرواتب والأجور ومذكرة التصفية،تقرير التعويضات، تقرير الحسميات. ويتميز البرنامج بكونه مخصص للاستخدام حسب صلاحيات يحددها مدير المنظومة فيها مجالات واسعة.\r\n', 'Remember salary information for calculating salaries program functions such as the employee''s salary, category, compensation, deductions, premiums, resettlement and other information. The program includes several reports, including the report of wages and salaries and a memorandum of liquidation, the compensation report, deductions report. The program is characterized by being dedicated for use by the powers defined by the system manager in broad areas.\r\n', 'png', 56, '2014-01-01 19:28:22', 0, 'exe', 0);
INSERT INTO `product` VALUES ('003', 'برنامج أوامر الدفع والقبض', 'Payment orders and arrest program', '001', 'برنامج أتمتة أوامر الدفع والقبض وادارة النفقات', 'Automation program payment orders and arrest and administration expenses', 'مهام برنامج أوامر الدفع والقبض حفظ كل الحركات الصادرة والواردة من اوامر دفع أو قبض وارتباطاتها مع جدول الميزانية. يتميز البرنامج بخاصية التذكير والمنع حيث يتم رصد مبالغ معينة لكل بند من بنود النفقات ويقوم البرنامج بالتنبيه عند الوصول إلى حد معين من الصرف أو منع إضافة أي مبغ اذا كان يسبب زيادة فوق المبلغ المرصود لبد معين.\r\n', 'Payment orders and capture program to save all the movements of incoming and outgoing orders to pay or catch and linkages with the budget schedule tasks. The program features a recall and prevention feature where a certain amount of monitoring for each item expenditure program and the alarm when you reach a certain extent from the exchange or prevent the addition of any MPEG If you cause an increase above a certain amount appropriated LAPD.\r\n', 'png', 55, '2014-01-01 19:30:12', 0, 'exe', 0);
INSERT INTO `product` VALUES ('004', 'برنامج المحاسبة والمستودعات', 'Accounting and warehouses program', '002', 'برنامج المحاسبة والمستودعات لأتمتة عمل الشركات', 'Accounting and warehouses program to automate the work of companies', 'مهام البرنامج أتمتة محاسبة أغلب شركات البيع بالجملة او البيع بالمفرق على كافة مجالات العمل وذلك من خلال حفظ فواتير البيع والشراء وفواتير المردود وغيرها. يتضمن البرنامج عدة تقارير منها تقرير الميزانية، تقرير دفتر استاذ، تقرير الأرباح والخسائر وغيرها.ويتميز البرنامج بكونه مخصص للاستخدام حسب صلاحيات يحددها مدير المنظومة فيها مجالات واسعة.\r\n', 'The program functions automate most of the sales companies accounting wholesale or retail on all areas of work and through the keeping of buying and selling and yield bills and other bills. The program includes several reports, including the budget report, ledger report, profit and loss report program Gerha.oeetmaz being dedicated for use by the powers defined by the system manager in broad areas.\r\n', 'png', 54, '2014-01-01 19:31:38', 0, 'exe', 0);
INSERT INTO `product` VALUES ('005', 'برنامج الصيادلة', 'Pharmacists program', '002', 'برنامج محاسبة ومستودعات خاص للصيادلة', 'Accounting and warehouses special program for pharmacists', 'مهام البرنامج أتمتة محاسبة الصيدليات وذلك من خلال حفظ فواتير البيع والشراء وفواتير المردود وغيرها.يتميز برنامج الصيدليات بأنه يتضمن أسماء جميع الأدوية المتداولة في سوق العمل مع سعرها الرسمي وجميع مواصفاتها. يتضمن البرنامج عدة تقارير منها تقرير الميزانية، تقرير دفتر استاذ، تقرير الأرباح والخسائر وغيرها.ويتميز البرنامج بكونه مخصص للاستخدام حسب صلاحيات يحددها مدير المنظومة فيها مجالات واسعة.\r\n', 'The program functions automate accounting pharmacies and through keeping sales and purchase invoices and bills yield pharmacies and Gerha.eetmaz program that includes the names of all traded in the labor market with the official price of drugs and all their specifications. The program includes several reports, including the budget report, ledger report, profit and loss report program Gerha.oeetmaz being dedicated for use by the powers defined by the system manager in broad areas.\r\n', 'png', 60, '2014-01-01 19:33:12', 0, '', 250.6);
INSERT INTO `product` VALUES ('006', 'برنامج شركة كهرباء دمشق', 'Damascus Electricity Company Program', '003', 'برنامج أتمتة فواتير شركة كهرباء دمشق ', 'Damascus electricity bills automation software company', 'مهام البرنامج أتمتة عمل شركة كهرباء دمشق من خلال حفظ كل المعلومات المتعلقة بعدادات الكهرباء من مكان التركيب، تاريخ التركيب، اسم المشترك، رقم هاتف المشترك، رقم العداد الحالي، رقم العداد السابق، كمية الاستهلاك وغيرها. يتضمن البرنامج طباعة فواتير الكهرباء للمشتركين بعد تسديد المبالغ المطلوبة. ويتميز البرنامج بكونه مخصص للاستخدام حسب صلاحيات يحددها مدير المنظومة فيها مجالات واسعة.\r\n', 'The program functions to automate the operation Damascus electricity company through keeping all information on metered electricity from the place of installation, date of installation, the common name, common phone number, number of the current counter, the former No. counter, the amount of consumption and others. The program includes print electricity bills to subscribers after paying the required amounts. The program is characterized by being dedicated for use by the powers defined by the system manager in broad areas.\r\n', 'png', 150, '2014-01-01 19:34:43', 0, 'exe', 0);
INSERT INTO `product` VALUES ('this is id', 'تجربة', 'تجربة', 'تجربة', 'تجربةتجربةتجربة', 'تجربةتجربةتجربة', 'تجربةتجربةتجربةتجربةتجربةتجربةتجربة', 'تجربةتجربةتجربةتجربةتجربةتجربةتجربة', NULL, 0, NULL, 0, NULL, 0);
INSERT INTO `product` VALUES ('54a810c8551ed', 'احمد الناشف', '', '003', 'احمد الناشفاحمد الناشفاحمد الناشف', '', 'احمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشفاحمد الناشف', '', 'jpg', 4, '2015-01-03 17:55:46', 1, '', 0);
INSERT INTO `product` VALUES ('54a9610ece334', '1', '2', '002', '', '', '', '', '', 0, '2015-01-04 17:49:38', 0, '', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `service`
-- 

CREATE TABLE `service` (
  `srv_id` varchar(32) default NULL,
  `srv_title_ar` varchar(150) default NULL,
  `srv_title_en` varchar(150) default NULL,
  `srv_desc_ar` text,
  `srv_desc_en` text,
  `NVNom` int(11) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `service`
-- 

INSERT INTO `service` VALUES ('001', 'خدمة الدعم الفني', 'Technical Support Service', ' خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني خدمة الدعم الفني', 'Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service Technical Support Service ', 0, NULL);
INSERT INTO `service` VALUES ('002', 'الخدمة الثانية', 'Second Service', 'الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانيةالخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانيةالخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانيةالخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانيةالخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانيةالخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانيةالخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية الخدمة الثانية', 'Second Service Second Service Second Service Second Service Second ServiceSecond Service Second Service Second Service Second Service Second ServiceSecond Service Second Service Second Service Second Service Second ServiceSecond Service Second Service Second Service Second Service Second ServiceSecond Service Second Service Second Service Second Service Second Service', NULL, NULL);
INSERT INTO `service` VALUES ('003', 'الخدمة الثالثة', 'Third Service', 'الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة الخدمة الثالثة ', 'Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service Third Service ', NULL, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `sys_otid`
-- 

CREATE TABLE `sys_otid` (
  `otid_valid_id` varchar(32) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `sys_otid`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tcount`
-- 

CREATE TABLE `tcount` (
  `countid` bigint(20) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `tcount`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ucomment`
-- 

CREATE TABLE `ucomment` (
  `ucom_id` varchar(32) default NULL,
  `ucom_user` varchar(32) default NULL,
  `ucom_text` text,
  `ucom_from_ip` varchar(19) default NULL,
  `ucom_reported` int(11) default '0',
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `ucomment`
-- 

INSERT INTO `ucomment` VALUES ('001', '004', 'البرنامج أنا أستخدمه من فترة طويلة وهو يعمل على مابرام ويلبي جميع احتياجاتي الضرورية\r\n', '1', 0, '2014-12-01 19:37:13');
INSERT INTO `ucomment` VALUES ('002', '004', 'هذا البرنامج أفضل من كل البرامج في سوق العمل وأنجحها على الاطلاق مشكورين على جهودكم.', '2', 0, '2014-12-09 19:39:04');

-- --------------------------------------------------------

-- 
-- Table structure for table `user`
-- 

CREATE TABLE `user` (
  `user_id` varchar(32) default NULL,
  `user_name` varchar(32) default NULL,
  `user_password` varchar(32) default NULL,
  `user_email` varchar(50) default NULL,
  `user_phone` varchar(20) default NULL,
  `user_address` varchar(100) default NULL,
  `user_country` varchar(20) default NULL,
  `user_city` varchar(20) default NULL,
  `user_birthyear` int(11) default NULL,
  `user_pic` varchar(32) default NULL,
  `user_cat` varchar(50) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `user`
-- 

INSERT INTO `user` VALUES ('549d99c87e38b', 'customer1', 'Passw0rd', 'customer@gmail.com', '+963949111333', 'Cairo st. 546', 'Egypt', 'Cairo', 1980, 'jpg', 'customer', '2014-12-26 19:24:24');
INSERT INTO `user` VALUES ('549d9068d6a90', 'a', 'a', 'a', '+963949111333', 'Almalkea', 'a', 'a', 1980, '', 'employee', NULL);
INSERT INTO `user` VALUES ('549d907f72efc', 'ssssss', 'a', 'a', '+963949111333', 'Almalkea', 'a', 'a', 1980, '', 'employee', NULL);
INSERT INTO `user` VALUES ('muhannadid', 'muhannad', '123', 'muhannad@gmail.com', '+963949111333', 'Almalkeasdfgfgs g adf af ', 'sdasdasd', 'ssss', 0, 'jpg', 'distributor', NULL);
INSERT INTO `user` VALUES ('549d99e3e0870', 'customer44', 'Passw0rd', 'customer2@gmail.com', '+963949111333', 'Address sf h 45yh45v45y', 'Egypt', 'Cairo', 1970, 'jpg', 'customer', '2014-12-26 19:24:51');
INSERT INTO `user` VALUES ('admin0000', 'admin', '123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `user` VALUES ('distID_ew3453463ef', 'Distributor Syria', '123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'distributor', NULL);
INSERT INTO `user` VALUES ('أحمد الناشف ID user', 'أحمد الناشف', '123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'employee', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `user_config`
-- 

CREATE TABLE `user_config` (
  `usrconf_user` varchar(32) NOT NULL,
  `usrconf_color` varchar(7) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `user_config`
-- 

INSERT INTO `user_config` VALUES ('6', 'ش');

-- --------------------------------------------------------

-- 
-- Table structure for table `user_groups`
-- 

CREATE TABLE `user_groups` (
  `user_id` varchar(32) default NULL,
  `user_group_id` varchar(32) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `user_groups`
-- 

INSERT INTO `user_groups` VALUES ('admin0000', 'admins0000');
INSERT INTO `user_groups` VALUES ('admin0000', 'myGR');
INSERT INTO `user_groups` VALUES ('muhannadid', 'muhannadGRid');
INSERT INTO `user_groups` VALUES ('أحمد الناشف ID user', 'أحمد الناشف ID');

-- --------------------------------------------------------

-- 
-- Table structure for table `user_group_privs`
-- 

CREATE TABLE `user_group_privs` (
  `group_id` varchar(32) default NULL,
  `priv` varchar(50) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `user_group_privs`
-- 

INSERT INTO `user_group_privs` VALUES ('admins0000', 'Aa');
INSERT INTO `user_group_privs` VALUES ('admins0000', 'myPRIV');
INSERT INTO `user_group_privs` VALUES ('myGR', 'PRIV 123');
INSERT INTO `user_group_privs` VALUES ('muhannadGRid', 'DOWNLOADS_MAN');
INSERT INTO `user_group_privs` VALUES ('muhannadGRid', 'AA');
INSERT INTO `user_group_privs` VALUES ('أحمد الناشف ID', 'PRODUCTS_MAN');
INSERT INTO `user_group_privs` VALUES ('muhannadGRid', 'NEWS_MAN');
INSERT INTO `user_group_privs` VALUES ('أحمد الناشف ID', 'NEWS_MAN');

-- --------------------------------------------------------

-- 
-- Table structure for table `user_privs`
-- 

CREATE TABLE `user_privs` (
  `user_id` varchar(32) default NULL,
  `user_priv` varchar(30) default NULL,
  `user_grant` tinyint(1) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `user_privs`
-- 

INSERT INTO `user_privs` VALUES ('admin0000', 'A', 1);
INSERT INTO `user_privs` VALUES ('أحمد الناشف ID user', 'ahmed_priv', NULL);
INSERT INTO `user_privs` VALUES ('أحمد الناشف ID user', 'BCONTS_MAN', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `video`
-- 

CREATE TABLE `video` (
  `vid_id` varchar(32) default NULL,
  `vid_title_ar` varchar(100) default NULL,
  `vid_title_en` varchar(100) default NULL,
  `vid_desc_ar` text,
  `vid_desc_en` text,
  `vid_link` varchar(200) default NULL,
  `NDate` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `video`
-- 

INSERT INTO `video` VALUES ('001', 'فيديو برنامج ذاتية الموظفين', 'Video Resume program staff', 'فيديو يشرح مهام برنامج الذاتية وكيفية حفظ كل المعلومات الذاتية للموظفين مثل المعلومات الشخصية، الوضع الوظيفي، الاجازات بلا اجر، العقوبات، الحالة الصحية وغيرها. ويتضمن الفيديو شرح كيفية التعامل مع تقارير البرنامج مثل تقرير الذاتية، تقرير عدد العاملين حسب عمر معين، تقرير الوضع الوظيفي للعاملين وغيره. ويتضمن الفيديو شرح إدارة صلاحيات البرنامج والتي يحددها مدير المنظومة وشرح مجالاتها الواسعة.\r\n', 'Video explains the functions of the self and how to save all self information to employees, such as personal information program, employment status, unpaid vacations, sanctions, health status, and others. And includes a video explaining how to deal with the program reports such as self-report, the report of the number of employees by a certain age, employment status report to employees and others. And includes a video explaining the powers of the management of the program as determined by the system manager and explain the broad fields.\r\n', 'https://www.youtube.com/watch?v=1rm', '2014-01-01 19:40:41');
INSERT INTO `video` VALUES ('002', 'فيديو برنامج رواتب وأجور', 'Video wages and salaries program', 'فيديو يشرح مهام برنامج الرواتب وكيفية حفظ المعلومات المتعلقة بحساب الرواتب مثل راتب الموظف،الفئة،التعويضات، الحسميات، الأقساط، معلومات التوطين وغيرها. يتضمن الفيديو شرح كيفية التعامل مع تقارير البرنامج  منها تقرير الرواتب والأجور ومذكرة التصفية،تقرير التعويضات، تقرير الحسميات. ويتضمن الفيديو شرح إدارة صلاحيات البرنامج والتي يحددها مدير المنظومة وشرح مجالاتها الواسعة.\r\n', 'Video explains the functions of a payroll program and how to save the information for calculating salaries, such as the employee''s salary, category, compensation, deductions, premiums, resettlement and other information. Includes a video explaining how to deal with the program reports, including the report of wages and salaries and a memorandum of liquidation, the compensation report, the report of deductions. And includes a video explaining the powers of the management of the program as determined by the system manager and explain the broad fields.\r\n', 'https://www.youtube.com/watch?v=1rm', '2014-01-01 19:41:53');
INSERT INTO `video` VALUES ('003', 'فيديو برنامج أوامر الدفع والقبض', 'Video payment orders and arrest program', 'فيديو يشرح مهام برنامج أوامر الدفع والقبض وكيفية حفظ كل الحركات الصادرة والواردة من اوامر دفع أو قبض وارتباطاتها مع جدول الميزانية. يتضمن الفيديو شرح كيفية التعامل مع خاصية التذكير والمنع حيث يتم رصد مبالغ معينة لكل بند من بنود النفقات ويقوم البرنامج بالتنبيه عند الوصول إلى حد معين من الصرف أو منع إضافة أي مبغ اذا كان يسبب زيادة فوق المبلغ المرصود لبد معين.\r\n', 'Video explains the functions of payment orders and how to capture and save all incoming and outgoing movements of the payment or receipt of orders and their linkages with the budget spreadsheet program. Includes a video explaining how to deal with reminders and prevention property where certain amounts are monitoring each item expenditure program and the alarm when you reach a certain extent from the exchange or prevent the addition of any MPEG If you cause an increase above a certain amount appropriated LAPD.\r\n', 'https://www.youtube.com/watch?v=1rm', '2014-01-01 19:42:46');
INSERT INTO `video` VALUES ('004', 'فيديو برنامج المحاسبة والمستودعات', 'Video accounting and warehouse program', 'فيديو يشرح مهام البرنامج على كافة مجالات عمل الشركات وذلك من خلال شرح كيفية حفظ فواتير البيع والشراء وفواتير المردود وغيرها. يتضمن الفيديو شرح لعدة تقارير منها تقرير الميزانية، تقرير دفتر استاذ، تقرير الأرباح والخسائر وغيرها.ويتضمن الفيديو شرح إدارة صلاحيات البرنامج والتي يحددها مدير المنظومة وشرح مجالاتها الواسعة.\r\n', 'Video explains the functions of the program to all areas of the company and that by explaining how to save buying and selling bills yield and other bills. Includes a video explaining to several reports, including the budget report, the report of the ledger, profit and loss report and Gerha.oatdmn video explaining the powers of the management of the program as determined by the system manager and explain the broad fields.\r\n', 'https://www.youtube.com/watch?v=1rm', '2014-01-01 19:43:38');
INSERT INTO `video` VALUES ('005', 'فيديو برنامج الصيادلة', 'Pharmacists video program', 'فيديو يشرح مهام البرنامج وكيفية عمل محاسبة الصيدليات وذلك من خلال شرح كيفية حفظ فواتير البيع والشراء وفواتير المردود وغيرها. يتضمن الفيديو شرح عدة تقارير منها تقرير الميزانية، تقرير دفتر استاذ، تقرير الأرباح والخسائر وغيرها.ويتضمن الفيديو أيضا شرح إدارة صلاحيات البرنامج والتي يحددها مدير المنظومة وشرح مجالاتها الواسعة.\r\n', 'Video explains the functions of the program and how the accounting pharmacies and so by explaining how to save buying and selling bills yield and other bills. Includes a video explaining several reports, including the budget report, ledger report, the report of profits and losses and Gerha.oatdmn video also explain the management powers of the program as determined by the system manager and explain the broad fields.\r\n', 'https://www.youtube.com/watch?v=1rm', '2014-01-01 19:44:25');
INSERT INTO `video` VALUES ('006', 'فيديو برنامج شركة كهرباء دمشق', 'Video Electric Company program Damascus', 'فيديو يشرح مهام برنامج أتمتة عمل شركة كهرباء دمشق من خلال شرح كيفية حفظ كل المعلومات المتعلقة بعدادات الكهرباء من مكان التركيب، تاريخ التركيب، اسم المشترك، رقم هاتف المشترك، رقم العداد الحالي، رقم العداد السابق، كمية الاستهلاك وغيرها. يتضمن الفيديو شرح كيفية طباعة فواتير الكهرباء للمشتركين بعد تسديد المبالغ المطلوبة. ويتضمن الفيديو شرح إدارة صلاحيات البرنامج والتي يحددها مدير المنظومة وشرح مجالاتها الواسعة.\r\n', 'Video explains the functions of the automation program of work Damascus electricity company by explaining how to save all information concerning metered electricity from the place of installation, date of installation, the common name, common phone number, number of the current counter, the former No. counter, the amount of consumption and others. Includes a video explaining how to print the electricity bills to subscribers after paying the required amounts. And includes a video explaining the powers of the management of the program as determined by the system manager and explain the broad fields.\r\n', 'https://www.youtube.com/watch?v=1rm', '2014-01-01 19:45:13');
