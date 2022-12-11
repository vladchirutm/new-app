CREATE TABLE public.authors (
	author_id serial4 NOT NULL,
	"name" text NOT NULL,
	CONSTRAINT author_pkey PRIMARY KEY (author_id)
);

CREATE TABLE public.books (
	book_id serial4 NOT NULL,
	title text NOT NULL,
	author_id serial4 NOT NULL,
	CONSTRAINT book_pkey PRIMARY KEY (book_id),
	CONSTRAINT books_un UNIQUE (author_id,title)
);

CREATE TABLE public.files (
	file_id serial4 NOT NULL,
	file text NOT NULL,
	import_date timestamp NOT NULL,
	CONSTRAINT file_pkey PRIMARY KEY (file_id)
);