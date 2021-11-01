import Resource from '@/api/resource';

class AuthorResource extends Resource {
  constructor() {
    super('authors');
  }
}

export { AuthorResource as default };
