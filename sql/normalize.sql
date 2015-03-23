update ingredients set header = replace(header, '<br><br>', ' ');
update ingredients set header = replace(header, ' </b>', '');

alter table ingredients drop column id;