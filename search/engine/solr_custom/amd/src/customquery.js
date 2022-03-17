define(['jquery'], function($) {
    var customquery = {};

    customquery.init = function() {
        customquery.addilterlistener();
        customquery.removefilterlistener();
    };

    customquery.addilterlistener = function() {
        $('.fieldtofind > .addfieldfilter').on('click', function(){
            let fielddata = customquery.getfielddata($(this));

            if (fielddata.currentquery === '*') {
                fielddata.queryinput.val(fielddata.newquery);
            } else {
                fielddata.queryinput.val(fielddata.currentquery + ' AND ' + fielddata.newquery.trimEnd());
            }

            fielddata.queryinput.parents('form').submit();
        });
    };

    customquery.removefilterlistener = function() {
        $('.fieldtofind > .removefieldfilter').on('click', function(){
            let fielddata;
            if ($(this).hasClass('deleteicon')) {
                fielddata = customquery.getfielddata($(this).parent());
            } else {
                fielddata = customquery.getfielddata($(this));
            }
            fielddata.newquery = customquery.removefieldfromquery(fielddata.currentquery, fielddata.newquery);

            let possible_leftovers = ['', '&&', '||', 'AND', 'OR', 'and', 'or']
            if (possible_leftovers.includes(fielddata.newquery.trim())) {
                fielddata.newquery = '*';
            }
            fielddata.queryinput.val(fielddata.newquery.trimEnd());

            fielddata.queryinput.parents('form').submit();
        });
    };

    customquery.getfielddata = function(fieldtofind) {
        let fieldname = fieldtofind.data('fieldname');
        let fieldvalue = fieldtofind.data('fieldvalue');
        let queryinput = $('form input#id_q');

        return {
            fieldname: fieldname,
            fieldvalue: fieldvalue,
            queryinput: queryinput,
            newquery: fieldname + ':"' + fieldvalue + '"',
            currentquery: queryinput.val()
        }
    };

    customquery.removefieldfromquery = function(currentquery, querytoremove) {
        let newquery = currentquery.replace('AND ' + querytoremove, '').trimStart();

        if (currentquery == newquery) {
            newquery = currentquery.replace(querytoremove, '').trimStart();
        }

        let possibleoperators = ['AND', 'OR', '&&', '||'];
        newquery = customquery.removeextraoperator(newquery, possibleoperators);

        return newquery;
    };

    customquery.removeextraoperator = function(query, operatorsarray) {
        let nooperatorquery;
        let newquery = query;

        operatorsarray.forEach(function(operator){
            nooperatorquery = query.replace(operator, '');
            if (nooperatorquery !== query) {
                newquery = nooperatorquery.trimStart();
            }
        });

        return newquery;
    };

    return customquery;
});