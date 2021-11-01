import Resource from '@/api/resource';
import request from '@/utils/request';

class TokenResource extends Resource {
  constructor() {
    super('tokens');
  }

  queriedTotalTokensSpent(query){
    return request({
      url: '/' + this.uri + '/queriedtotal',
      method: 'get',
      params: query,
    });
  }
}

export { TokenResource as default };
