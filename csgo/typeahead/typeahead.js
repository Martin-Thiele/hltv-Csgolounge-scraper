 	// Team typeahead
    $(document).ready(function(){
    $('input.typeahead').typeahead({
    	logo: 'typeahead',
        name: 'typeahead',
        remote:'//brintos.dk/csgo/typeahead/team.php?key=%QUERY',
        limit : 10
    });
});
    // Subcomp typeahead
    $(document).ready(function(){
    $('input.typeahead2').typeahead({
    	logo: 'typeahead2',
        name: 'typeahead2',
        remote:'//brintos.dk/csgo/typeahead/subcomp.php?key=%QUERY',
        limit : 10
    });
});
    // Map typeahead
    $(document).ready(function(){
    $('input.typeahead3').typeahead({
    	logo: 'typeahead3',
        name: 'typeahead3',
        remote:'//brintos.dk/csgo/typeahead/map.php?key=%QUERY',
        limit : 10
    });
});
    // Playernoteam typeahead
    $(document).ready(function(){
    $('input.typeahead4').typeahead({
    	logo: 'typeahead4',
        name: 'typeahead4',
        remote:'//brintos.dk/csgo/typeahead/playernoteam.php?key=%QUERY',
        limit : 10
    });
});
    // Subcompnocomp typeahead
    $(document).ready(function(){
    $('input.typeahead5').typeahead({
        logo: 'typeahead5',
        name: 'typeahead5',
        remote:'//brintos.dk/csgo/typeahead/subcompnocomp.php?key=%QUERY',
        limit : 10
    });
});
    // comp typeahead
    $(document).ready(function(){
    $('input.typeahead6').typeahead({
        logo: 'typeahead6',
        name: 'typeahead6',
        remote:'//brintos.dk/csgo/typeahead/comp.php?key=%QUERY',
        limit : 10
    });
});
