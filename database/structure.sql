
CREATE TABLE `Belongs_to` (
  `pid` int(11) NOT NULL,
  `tid` int(11) NOT NULL,
  `prim` tinyint(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Triggers `Belongs_to`
--
DELIMITER $$
CREATE TRIGGER `playertransfer` BEFORE UPDATE ON `Belongs_to` FOR EACH ROW INSERT INTO Playertransfers
   ( id,
     pid,
     tid1,
   	 tid2,
     transdate)
   VALUES
   ( DEFAULT,
     NEW.pid,
     OLD.tid,
   	 NEW.tid,
     CURDATE())
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Competitions`
--

CREATE TABLE `Competitions` (
  `cid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Comp_belongs_to`
--

CREATE TABLE `Comp_belongs_to` (
  `subcid` int(11) NOT NULL,
  `cid` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Maps`
--

CREATE TABLE `Maps` (
  `mapid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Map_belongs_to`
--

CREATE TABLE `Map_belongs_to` (
  `id` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `pmapid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Matches`
--

CREATE TABLE `Matches` (
  `mid` int(11) NOT NULL,
  `tid_1` int(11) DEFAULT NULL,
  `tid_2` int(11) DEFAULT NULL,
  `match_date` date DEFAULT NULL,
  `match_time` varchar(5) DEFAULT NULL,
  `subcid` int(11) DEFAULT NULL,
  `complete` tinyint(1) DEFAULT NULL,
  `csgl` varchar(70) DEFAULT NULL,
  `hltv` varchar(65) DEFAULT NULL,
  `csglodds1` int(3) DEFAULT NULL,
  `csglodds2` int(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Playedmaps`
--

CREATE TABLE `Playedmaps` (
  `pmapid` int(11) NOT NULL,
  `mapid` int(11) DEFAULT NULL,
  `score_1` int(11) DEFAULT NULL,
  `score_2` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Players`
--

CREATE TABLE `Players` (
  `pid` int(11) NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `ign` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `twitter` varchar(50) DEFAULT NULL,
  `facebook` varchar(80) DEFAULT NULL,
  `twitch` varchar(50) DEFAULT NULL,
  `steam` varchar(70) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Playertransfers`
--

CREATE TABLE `Playertransfers` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `tid1` int(11) NOT NULL,
  `tid2` int(11) NOT NULL,
  `transdate` date DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Subcomp`
--

CREATE TABLE `Subcomp` (
  `subcid` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `Teams`
--

CREATE TABLE `Teams` (
  `tid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `tcountry` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `hltvname` varchar(255) DEFAULT NULL,
  `csglname` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Belongs_to`
--
ALTER TABLE `Belongs_to`
  ADD KEY `tid` (`tid`);

--
-- Indexes for table `Competitions`
--
ALTER TABLE `Competitions`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `cid` (`cid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `Comp_belongs_to`
--
ALTER TABLE `Comp_belongs_to`
  ADD UNIQUE KEY `subcid` (`subcid`),
  ADD KEY `cid` (`cid`);

--
-- Indexes for table `Maps`
--
ALTER TABLE `Maps`
  ADD PRIMARY KEY (`mapid`),
  ADD UNIQUE KEY `mapid` (`mapid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `Map_belongs_to`
--
ALTER TABLE `Map_belongs_to`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Map_belongs_to_ibfk_1` (`mid`),
  ADD KEY `Map_belongs_to_ibfk_2` (`pmapid`);

--
-- Indexes for table `Matches`
--
ALTER TABLE `Matches`
  ADD PRIMARY KEY (`mid`);

--
-- Indexes for table `Playedmaps`
--
ALTER TABLE `Playedmaps`
  ADD PRIMARY KEY (`pmapid`);

--
-- Indexes for table `Players`
--
ALTER TABLE `Players`
  ADD PRIMARY KEY (`pid`),
  ADD UNIQUE KEY `pid` (`pid`);

--
-- Indexes for table `Playertransfers`
--
ALTER TABLE `Playertransfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`),
  ADD KEY `tid1` (`tid1`),
  ADD KEY `tid2` (`tid2`);

--
-- Indexes for table `Subcomp`
--
ALTER TABLE `Subcomp`
  ADD PRIMARY KEY (`subcid`),
  ADD UNIQUE KEY `subcid` (`subcid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `Teams`
--
ALTER TABLE `Teams`
  ADD PRIMARY KEY (`tid`),
  ADD UNIQUE KEY `tid` (`tid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Competitions`
--
ALTER TABLE `Competitions`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;
--
-- AUTO_INCREMENT for table `Maps`
--
ALTER TABLE `Maps`
  MODIFY `mapid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT for table `Map_belongs_to`
--
ALTER TABLE `Map_belongs_to`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52003;
--
-- AUTO_INCREMENT for table `Matches`
--
ALTER TABLE `Matches`
  MODIFY `mid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7444;
--
-- AUTO_INCREMENT for table `Playedmaps`
--
ALTER TABLE `Playedmaps`
  MODIFY `pmapid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15369;
--
-- AUTO_INCREMENT for table `Players`
--
ALTER TABLE `Players`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1085;
--
-- AUTO_INCREMENT for table `Playertransfers`
--
ALTER TABLE `Playertransfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1444;
--
-- AUTO_INCREMENT for table `Subcomp`
--
ALTER TABLE `Subcomp`
  MODIFY `subcid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=529;
--
-- AUTO_INCREMENT for table `Teams`
--
ALTER TABLE `Teams`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1210;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `Map_belongs_to`
--
ALTER TABLE `Map_belongs_to`
  ADD CONSTRAINT `Map_belongs_to_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Matches` (`mid`) ON DELETE CASCADE,
  ADD CONSTRAINT `Map_belongs_to_ibfk_2` FOREIGN KEY (`pmapid`) REFERENCES `Playedmaps` (`pmapid`) ON DELETE CASCADE;
